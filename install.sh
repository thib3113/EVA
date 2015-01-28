#! /bin/bash
#
ver_install=1.0.13
url_git="https://github.com/thib3113/EVA.git"

default_branch="dev"
log_folder="/var/log/eva"
log_file="install.log"
log_error_file="error.install.log"
install_folder="/var/www/EVA"

GREEN="\\033[1;32m"
WHITE="\\033[0;39m"
RED="\\033[1;31m"
BLUE='\e[0;34m'
PURPLE='\e[0;35m'

#on traite les arguments
dev_mod=0
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

install(){
    cmd "apt-get install -y $1"

    check_install $1
    if [ $? -eq 1 ];
    then
        affich error
    else
        affich point
    fi
}




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

log(){
    if [[ ! -z $2 ]]
        then
        type=" ${2} : " 
    else
        type=""
    fi

    echo -e "$(date '+%d/%m/%Y %T') :${type} $1" >> "$log_folder/$log_file" 2>> "$log_folder/$log_error_file"
}

cmd(){
    if [[ -z $1 ]]
        then
        return 1
    fi

    log "$1" cmd
    $1 >> "$log_folder/$log_file" 2>> "$log_folder/$log_error_file";
}


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

secure_install curl
ver_eva=$(curl -s https://raw.githubusercontent.com/thib3113/EVA/${branche}/static.php | grep PROGRAM_VERSION | sed 's/[^0-9.]*\([0-9.]*\).*/\1/')

if [ -z $ver_eva ] ; then
   echo -ne "Impossible de récupéré la version de EVA " ;
   affich error
fi

GRN=$GREEN

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


echo -e "Installation EVA \n date : $(date '+%d/%m/%Y %T') \n version eva : $ver_eva \n version installeur $ver_install" >> "$log_folder/$log_file"
if [ $dev_mod -eq 1 ]
then
    echo -e "${PURPLE}Mode developpeur activé${WHITE}" >> "$log_folder/$log_file"
fi
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


affich action "Mise à jour de la liste des paquets "
    affich point
    affich point
 cmd "sudo apt-get update" 
    affich point
affich ok

secure_install git-core

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

if [ -z $webserver ]
then
    secure_install lighttpd

    secure_install php5
    secure_install php5-cgi
    secure_install sqlite
    secure_install php5-sqlite
    secure_install php5-curl
    cmd "/etc/init.d/lighttpd force-reload"

    affich action "Configuration de lighttpd "
    cmd "sudo lighttpd-enable-mod fastcgi-php"
        affich point
    cmd "sudo lighttpd-enable-mod cgi"
        affich point
    cmd "sudo /etc/init.d/lighttpd force-reload"
        affich point
    affich ok
fi

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

affich action "Création d'un dossier pour EVA"
affich point
if [ ! -d $install_folder ]
then
        cmd "mkdir -p $install_folder"
        cmd "chown -R eva $install_folder"
        affich ok
    else
        cmd "chown -R eva $install_folder"
        affich already
fi

if [ $(ls -a $install_folder 2>> "$log_folder/$log_error_file" | sed -e "/\.$/d" | wc -l  ) -ne 0 ] && [ $dev_mod -ne 1 ]
then
    read -p "la suite va supprimer le contenu du dossier $install_folder, voulez vous continuer [o/N] : " REP

    case $REP in
                 O|o)
                    cmd "rm -Rf $install_folder/*"
                    log "Contenu du dossier $install_folder éffacé"
                ;;
                 N|n|*)
                       log "vous avez répondu "$REP" installation avortée"
                       echo -e " Eva ne sera pas installé "
                       exit 1
                 ;;
    esac
fi

affich action "Création d'un utilisateur eva "
affich point
affich point
cmd "useradd --home /home/eva eva"
affich point
cmd "sudo -u eva cat /var/log/eva/install.log" 
affich point
if [ ! $? -eq 1 ]
then
    affich ok
else
    affich error
fi

ligne_sudoers="git ALL=(eva) ALL"
if [ $(cat /etc/sudoers | grep -v "#" |  grep "^git.*\(eva\)" | wc -l) -gt 0 ]
    then
    echo -e "Eva doit modifier le fichier sudoers, cependant, il semble que votre fichier contient déjà une règle à propos de eva \n vous devez rajouter/modifier la ligne comme ceci : $ligne_sudoers"
    log "Eva doit modifier le fichier sudoers, cependant, il semble que votre fichier contient déjà une règle à propos de eva \n vous devez rajouter/modifier la ligne comme ceci : $ligne_sudoers"
    read -p "[appuyer sur entrée]"
else
    read -p "Eva doit modifier le fichier sudoers, voulez vous le faire automatiquement ( conseillé ) [o/N] : " REP

    case $REP in
                 O|o)
                    affich action "Modification du fichier sudoers "
                    log "cat /etc/sudoers >> sudoers.tmp" 
                    cat /etc/sudoers > sudoers.tmp
                            affich point
                    cat sudoers.tmp > "$log_folder/sudoers.backup"
                    echo $ligne_sudoers >> sudoers.tmp
                    cat sudoers.tmp > "$log_folder/sudoers.modif.log"
                            affich point
                    cmd "visudo -c -f sudoers.tmp"
                            affich point
                    if [ $? -eq 1 ];
                    then
                        affich error
                    else
                        cat sudoers.tmp > /etc/sudoers
                        affich ok
                    fi
                    ;;
                 N|n|*)
                       echo -e " vous devez rentrer la ligne suivante dans ton sudoers : \n $ligne_sudoers "
                       log " vous devez rentrer la ligne suivante dans ton sudoers : \n $ligne_sudoers "
                 ;;
    esac
    
fi

affich action "Clonage de Eva "
affich point
if [ $dev_mod -ne 1 ]
then
    cmd "sudo -u eva git clone --verbose $url_git --branch $branche --single-branch $install_folder"
else
    log "Pas de clone en mode developpeur"
fi

cmd "chmod -R 775 $install_folder"
affich point
affich point
if [ $(ls -a $install_folder 2>> "$log_folder/$log_error_file" | sed -e "/\.$/d" | wc -l ) -ne 0 ]
    then
    affich ok
else
    log "dossier cloné vide !"
    affich error
fi

