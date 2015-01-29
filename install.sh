#! /bin/bash

##############################
## définition des variables ##
##############################

# version de l'installateur, pour le debug
ver_install=1.0.19
# url du dépot à télécharger
url_git="https://github.com/thib3113/EVA.git"

# branche télécharger par défault
default_branch="dev"

# nom des logs
log_folder="/var/log/eva"
log_file="install.log"
log_error_file="error.install.log"

# dossier d'installation
install_folder="/var/www/EVA"

# variable de couleur
GREEN="\\033[1;32m"
WHITE="\\033[0;39m"
RED="\\033[1;31m"
BLUE='\e[0;34m'
PURPLE='\e[0;35m'

###########################
## gestion des arguments ##
###########################
#les argument sont :
#                   -d : active le mode developpeur
#                   -b:<branche> : selectionne la branche à installer ( dev/master )
dev_mod=0
#on regarde les arguments passé
while getopts "db:" opt; do
  case $opt in
    d)
      dev_mod=1
      echo -e "${PURPLE}Mode developpeur activé${WHITE}"
      ;;
    b)
      branche=$(echo $OPTARG | cut -c2-)
      ;;
    :)
      echo "L'option -$OPTARG requière un argument." >&2
      exit 1
      ;;
  esac
done

#on met les defaults si ils les variables sont vide
if [ -z $branche ]
then
    branche=${default_branch}
fi

case $branche in
    master);;
    dev);;
    *)
        branche=${default_branch}
      ;;
  esac

# fonction d'affichage
#       action : permet la description d'une action, necessite un paramètre supplémentaire
#       ok : permet d'afficher un [ok] en vert
#       error : permet d'afficher un [error] en rouge, termine le script
#       already : permet d'afficher un [deja fait] en bleu
#       point : permet d'afficher un .
# affich ok|error|already|point|(action texte)
#chaque fonction affiche le texte et le log
affich(){
    format=""
    for var in "$@"
    do
        if [ $(echo "action ok error already point " | grep -- "$var\s" | wc -l) -eq 1 ]
        then
            format=$var
        fi

        if [ "$var" == "-no-log" ]
        then
            no_log=1
        fi

        text=$var
    done

    case $format in
        "action")
            echo -ne "${PURPLE}- ${text}${WHITE}"
            if [[ $no_log != 1 ]]
            then
                log "${PURPLE}- ${text}${WHITE}" status
            fi
        ;;
        "ok")
            echo -e "[${GREEN}FAIT${WHITE}]"
            if [[ $no_log != 1 ]]
            then
                log  "[${GREEN}FAIT${WHITE}]" status
            fi
        ;;
        "error")
            echo -e "[${RED}ERREUR${WHITE}]"
            if [[ $no_log != 1 ]]
            then
                log  "[${RED}ERREUR${WHITE}]" status
            fi
            exit 1
        ;;
        "already")
            echo -e "[${BLUE}DEJA FAIT${WHITE}]"
            if [[ $no_log != 1 ]]
            then
                log  "[${BLUE}DEJA FAIT${WHITE}]" status
            fi
        ;;
        "point")
            echo -ne ". "
            if [[ $no_log != 1 ]]
            then
               log ". "
            fi
        ;;
    esac
}

# vérifie si un logiciel est installé
# check_install logiciel
# /!\ en shell 0=true, 1=false
check_install(){

    soft="$1"

    log "sudo dpkg --get-selections | grep \"^$soft\s\" | grep -v deinstall" cmd
    if [ $(sudo dpkg --get-selections  | grep "^$soft\s" | grep -v deinstall  | wc -l) -gt 0 ]
    then
        return 0
    else
        return 1
    fi
}

# permet d'installer un logiciel, puis de vérifié l'installation, l'installation retente 3 fois puis erreur
# secure_install logiciel
secure_install(){
    i=0;
    affich action "tentative d'installation de $1 "
    if check_install $1
    then
            affich point
            affich point
            affich point
        affich already
        return
    fi

    while ! `check_install $1` && [ $i -lt 3 ]
    do
        cmd "apt-get install -y $1"
            affich point
            affich point
        i=$(($i + 1))
    done

    if [[ $i -lt 3 ]]; then
        affich ok
    else
        affich error
    fi
}


#on regarde si la librairie gpio est là ( wiringPi pour le coup )
check_gpio(){
    return 0
    cmd "gpio -v"
    if [[ "$?" != "0" ]]
    then
        gpio -v 2> /dev/null > /dev/null
        if [[ $? == 1 ]]
            then
            log "${RED}Votre appareil ne semble pas disposer de gpio, ou la librairie GPIO n'est pas installé${WHITE}"
            affich error
        fi
        return 1
    else
        return 0
    fi
}

#permet d'ajouter au fichier de log.
#   log text [type]
log(){
    if [[ ! -z $2 ]]
        then
        type=" ${2} : " 
    else
        type=""
    fi

    echo -e "$(date '+%d/%m/%Y %T') :${type} $1" >> "$log_folder/$log_file" 2>> "$log_folder/$log_error_file"
}

#permet d'éxécuter une commande ( et de logger automatiquement ses retours )
# cmd commande
cmd(){
    if [[ -z $1 ]]
        then
        return 1
    fi

    log "$1" cmd
    $1 >> "$log_folder/$log_file" 2>> "$log_folder/$log_error_file";
}

# on vérifie que le script soit lancé en root
if [ "$UID" -ne 0 ]
then
    echo -e "Le script doit être lancé en sudo [${RED}ERREUR${WHITE}]"
    exit 0
fi

#on crée le dossier de log au besoin
if [ ! -d $log_folder ]
then
    mkdir -p $log_folder
fi

#on essaye d'installer curl, car necessaire sur les logs
secure_install curl
#on récupère la version de eva sur la branche en cours
ver_eva=$(curl -s https://raw.githubusercontent.com/thib3113/EVA/${branche}/static.php | grep PROGRAM_VERSION | sed 's/[^0-9.]*\([0-9.]*\).*/\1/')

#si on ne peux pas récupéré la version d'eva, on peux avoir d'autre problème, on sort de suite
if [ -z $ver_eva ] ; then
   echo -ne "Impossible de récupéré la version de EVA " ;
   affich error
fi

#on raccourcis la variable green pour l'alignement
GRN=$GREEN

#on génére la présentation de eva
echo -e "${GRN}   .~~.   .~~.   ${WHITE}                                   "
echo -e "${GRN}  '. \ ' ' / .'  ${WHITE}                                   "
echo -e "${RED}   .~ .~~~..~.   ${WHITE}   ________  ____   ____    _$ver_eva"
echo -e "${RED}  : .~.'~'.~. :  ${WHITE}  |_   __  ||_  _| |_  _|  / \     "
echo -e "${RED} ~ (   ) (   ) ~ ${WHITE}    | |_ \_|  \ \   / /   / _ \    "
echo -e "${RED}( : '~'.~.'~' : )${WHITE}    |  _| _    \ \ / /   / ___ \   "
echo -e "${RED} ~ .~ (   ) ~. ~ ${WHITE}   _| |__/ |    \ ' /  _/ /   \ \_ "
echo -e "${RED}  (  : '~' :  )  ${WHITE}  |________|     \_/  |____| |____|"
echo -e "${RED}   '~ .~~~. ~'   ${WHITE}                                   "
echo -e "${RED}       '~'       ${WHITE}  branch $branche - Installeur $ver_install "
echo -e "Lien github : ${GREEN}https://github.com/thib3113/EVA${WHITE}"
echo -e "Lien du site : ${GREEN}http://evaproject.net/${WHITE}"
echo -e "En cas de problème, merci de joindre les fichiers présent dans le dossier ${log_folder} avec votre problème"
echo -e "${GREEN}Crée par SEVERAC Thibaut - étudiant - ${PURPLE}http://cv.thib3113.fr${WHITE}\n"

#on log les versions pour de l'aide plus tard
log "Installation EVA \n date : $(date '+%d/%m/%Y %T') \n version eva : $ver_eva \n version installeur $ver_install"

#on avertis la personne si elle est en mode developpeur
if [ $dev_mod -eq 1 ]
then
    echo -e "${PURPLE}Mode developpeur activé${WHITE}" >> "$log_folder/$log_file"
fi

#on se déplace dans le /var/www, on le crée si il n'existe pas
affich action "Déplacement dans /var/www "
if [ -d /var/www ]
then
        affich point
        affich point
    cmd "cd /var/www"
        affich point
    affich ok
else
        affich point
    cmd "mkdir -p /var/www"
    log "création de /var/www réussie "
    echo -ne "création réussie "
        affich point
    cmd "cd /var/www"
        affich point
    affich ok
fi

#on met à jour la liste de paquet
affich action "Mise à jour de la liste des paquets "
    affich point
    affich point
 cmd "sudo apt-get update" 
    affich point
affich ok

#on installe git
secure_install git-core

#on cherche l'existence d'un serveur web déjà installé
affich action "Recherche d'un serveur web "
if check_install apache2;
then
    webserver="Apache"
fi

    affich point


if check_install nginx;
then
    webserver="Nginx"
fi

    affich point

if check_install lighttpd;
then
    webserver="Lighttpd"
fi

    affich point

#on indique le résultat de la recherche précédente
if [ ! -z $webserver ]
then
    echo -ne "${webserver} est déjà installé ! "
    log "${webserver} est déjà installé ! "
    affich already
else
    echo -ne "Aucun serveur web installé ! "
    log "Aucun serveur web installé ! "
    affich ok
fi

#si il n'y à pas de web server, on installe lighttpd + php + sqlite
if [ -z $webserver ]
then
    #on fait les différentes installations
    secure_install lighttpd
    secure_install php5
    secure_install php5-cgi
    secure_install sqlite
    secure_install php5-sqlite
    secure_install php5-curl

    #on reload lighttpd
    cmd "/etc/init.d/lighttpd force-reload"

    affich action "Configuration de lighttpd "
    #on active php
    cmd "sudo lighttpd-enable-mod fastcgi-php"
        affich point
    #on active cgi
    cmd "sudo lighttpd-enable-mod cgi"
        affich point
    #on reload lighttpd
    cmd "sudo /etc/init.d/lighttpd force-reload"
        affich point
    affich ok
fi

#on installe wiringPi si il ne l'est pas déjà
affich action "Installation de wiringPi "
affich point
if check_gpio ;
then
    affich point
    affich point
    affich already
else
    cmd "git clone --verbose git://git.drogon.net/wiringPi ~/wiringPi"
        affich point
    log "~/wiringPi/./build" cmd
    cmd "cd ~/wiringPi"
    cmd "./build"
        affich point
    cmd "cd -"
        affich point


    if check_gpio ;
    then
        affich ok
    else
        affich error
    fi
fi

#on crée un dossier pour eva, avec les droit correspondant pour l'utilisateur eva
affich action "Création d'un dossier pour EVA"
affich point
if [ ! -d $install_folder ]
then
        cmd "mkdir -p $install_folder"
        cmd "chown -R eva:pi $install_folder"
        affich ok
    else
        cmd "chown -R eva:pi $install_folder"
        affich already
fi

#on crée l'utilisateur eva
affich action "Création d'un utilisateur eva "
affich point
affich point
# cmd "useradd --home /home/eva eva"
cmd "adduser --system eva"
affich point
cmd "sudo -u eva cat /var/log/eva/install.log" 
affich point
if [ ! $? -eq 1 ]
then
    affich ok
else
    affich error
fi

#on regarde si le dossier ne contient pas quelque chose , sinon on demande à l'utilisateur si il est sur de vouloir supprimer
if [ $(ls -a $install_folder 2>> "$log_folder/$log_error_file" | sed -e "/\.$/d" | wc -l  ) -ne 0 ] && [ $dev_mod -ne 1 ]
then
    read -p "la suite va supprimer le contenu du dossier $install_folder, voulez vous continuer [o/N] : " REP

    case $REP in
                 O|o)
                    cmd "rm -Rf $install_folder/*"
                    cmd "rm -Rf $install_folder/.*"
                    log "Contenu du dossier $install_folder éffacé"
                ;;
                 N|n|*)
                       log "vous avez répondu "$REP" installation avortée"
                       echo -e " Eva ne sera pas installé "
                       exit 1
                 ;;
    esac
fi

#on ajoute une ligne dans le fichier sudoers
#vu que le fichier est sensible, on fait un tmp au passage, que l'on check avec visudo
ligne_sudoers_git="git ALL=(eva) ALL"
ligne_sudoers="${ligne_sudoers_git}\nwww-data ALL=(eva) NOPASSWD: ALL"
if [ $(cat /etc/sudoers | grep -v "#" |  grep "^git.*\(eva\)" | wc -l) -gt 0 ]
    then
    #si on passe ici, le fichier sudoers contient déjà des information sur les droit d'eva sur git
    echo -ne "Eva doit modifier le fichier sudoers, cependant, il semble que votre fichier contient déjà une règle à propos de eva \n vous devez rajouter/modifier les ligne comme ceci : $ligne_sudoers\n"
    log "Eva doit modifier le fichier sudoers, cependant, il semble que votre fichier contient déjà une règle à propos de eva \n vous devez rajouter/modifier les ligne comme ceci :\n$ligne_sudoers"
    read -p "[appuyer sur entrée]"
else
    #on demande à l'utilisateur si il veux le faire
    read -p "Eva doit modifier le fichier sudoers, voulez vous le faire automatiquement ( conseillé ) [o/N] : " REP

    case $REP in
                 O|o)
                    #si oui
                    affich action "Modification du fichier sudoers "
                    #on crée un clone du fichier sudoers
                    log "cat /etc/sudoers >> sudoers.tmp" 
                    cat /etc/sudoers > sudoers.tmp
                            affich point
                    #on crée un backup dans les log du fichier sudoers
                    cat sudoers.tmp > "$log_folder/sudoers.backup"
                    #on ajoute notre ligne à la fin du fichier sudoers
                    echo -e $ligne_sudoers >> sudoers.tmp
                    #on log le nouveau fichier sudoers
                    cat sudoers.tmp > "$log_folder/sudoers.modif.log"
                            affich point
                    #on demande à visudo d'analyser notre nouveau fichier sudoers
                    cmd "visudo -c -f sudoers.tmp"
                            affich point
                    if [ $? -eq 1 ];
                    then
                        #le nouveau fichier n'est pas bon, on sort une erreur
                        affich error
                    else
                        #le nouveau fichier est bon, on modifie le vrai fichier par le contenu de notre sudoers temporaire
                        cat sudoers.tmp > /etc/sudoers
                            affich point
                        #on supprime le temporaire
                        rm sudoers.tmp
                        affich ok
                    fi
                    ;;
                 N|n|*)
                       # la personne préfère le faire manuellement, on lui affiche donc la ligne à rentrer, et on la lui met dans les logs
                       echo -e " vous devez rentrer la ligne suivante dans ton sudoers : \n $ligne_sudoers "
                       log " vous devez rentrer la ligne suivante dans ton sudoers : \n $ligne_sudoers "
                 ;;
    esac
    
fi

#on clone eva avec la branche
affich action "Clonage de Eva "
affich point
# si on est en dev_mod on ne clone pas
if [ $dev_mod -ne 1 ]
then
    #on fait un clone de la branche choisie dans le dossier d'installation
    cmd "sudo -u eva git clone --verbose $url_git --branch $branche --single-branch $install_folder"
else
    log "Pas de clone en mode developpeur"
fi

#on change les droits du dossier d'installation
cmd "chmod -R 775 $install_folder"
cmd "chgrp -R pi $install_folder"
#on met les droits en écriture sur le cache, la db, les plugins, et les logs
cmd "chmod -R 777 $install_folder/cache $install_folder/db $install_folder/plugins $install_folder/log"
#on met les droits à eva sur le dossier de log
cmd "chown -R eva $log_folder"
cmd "chmod -R 775 $log_folder"
affich point
affich point
#on regarde si ça à marché en regardant si le dossier d'installation est vide
if [ $(ls -a $install_folder 2>> "$log_folder/$log_error_file" | sed -e "/\.$/d" | wc -l ) -ne 0 ]
    then
    affich ok
else
    log "dossier cloné vide !"
    affich error
fi

if [ $branche == "dev" ]
    then
    debug=1
else
    debug=0
fi
#si on arrive ici tout va bien, on génère le fichier de configuration :
fichier_conf="<?php\n\trequire __DIR__.'/static.php';\n\n\tdefine('DB_TYPE','SQLITE');\n\tdefine('DB_PREFIX','EVA_');\n\tdefine('SYSTEM_USER', 'eva');\n\tdefine('DB_NAME', ROOT.'/db/.database.db');\n\tdefine('DB_HASH','sha512');\n\tdefine('LOG_FILE', '$log_folder/log.txt');\n\tdefine('DEBUG', $debug);"
log "on crée un fichier de config : \n $fichier_conf"
echo -e $fichier_conf > $install_folder/config.php

#on log la fin dans le fichier
log "########################"
log "## fin d'installation ##"
log "########################"

#TODO : système de post automatique de demande d'aide, avec un zip des logs