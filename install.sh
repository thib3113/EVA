#! /bin/bash
#
ver_install=1.0.7
url_git="https://github.com/thib3113/EVA.git"

default_branch="dev"
log_folder="/var/log/eva"
log_file="install.log"
log_error_file="error.install.log"

GREEN="\\033[1;32m"
WHITE="\\033[0;39m"
RED="\\033[1;31m"
BLUE='\e[0;34m'
PURPLE='\e[0;35m'

#on traite les arguments
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
            if [ ! -z $no_log ]
            then
                echo -ne "${PURPLE}- ${text}${WHITE}" >> "$log_folder/$log_file"
            fi
        ;;
        "ok")
            echo -e "[${GREEN}FAIT${WHITE}]"
            if [ ! -z $no_log ]
            then
                echo -e "[${GREEN}FAIT${WHITE}]" >> "$log_folder/$log_file"
            fi
        ;;
        "error")
            echo -e "[${RED}ERREUR${WHITE}]"
            if [ ! -z $no_log ]
            then
                echo -e "[${RED}ERREUR${WHITE}]" >> "$log_folder/$log_file"
            fi
            exit 1
        ;;
        "already")
            echo -e "[${BLUE}DEJA FAIT${WHITE}]"
            if [ ! -z $no_log ]
            then
                echo -e "[${BLUE}DEJA FAIT${WHITE}]" >> "$log_folder/$log_file"
            fi
        ;;
        "point")
            echo -ne ". "
            if [ ! -z $no_log ]
            then
                echo -ne ". " >> "$log_folder/$log_file"
            fi
        ;;
    esac
}

check_install(){
    if [ -z $2 ] || [ $2 != "-r"]
    then
        soft="${1}\s"
    else
        soft=$1
    fi
    # echo "sudo dpkg --get-selections | grep \"$soft\" | grep -v deinstall"
    if [ $(sudo dpkg --get-selections | grep "$soft" | grep -v deinstall | wc -l) -gt 0 ]
    then
        return 0
    else
        return 1
    fi
}

install(){
    echo "apt-get install -y $1" >> "$log_folder/$log_file"
    apt-get install -y $1 >> "$log_folder/$log_file" 2>> "$log_folder/$log_error_file"

    check_install $1
    if [ $? -eq 1 ];
    then
        affich error
    else
        affich point
    fi
}

if [ "$UID" -ne 0 ]
then
    echo -e "Le script doit être lancé en sudo [${RED}ERREUR${WHITE}]"
    exit 0
fi


ver_eva=$(curl -s https://raw.githubusercontent.com/thib3113/EVA/${branche}/config.php | grep PROGRAM_VERSION | sed 's/[^0-9.]*\([0-9.]*\).*/\1/')

if [ -z $ver_eva ] ; then
   echo -ne "Impossible de récupéré la version de EVA " ;affich error
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
echo -e "En cas de problème, merci de joindre vos deux fichiers présent dans le dossier log avec votre problème"
echo -e "${GREEN}Crée par SEVERAC Thibaut - étudiant - ${PURPLE}http://cv.thib3113.fr${WHITE}\n"

affich action "Création d'un dossier pour les logs "
if [ -d $log_folder ]
then
        affich -no-log point
        affich -no-log point
        affich -no-log point
    affich already
else
        affich -no-log point
    mkdir -p $log_folder
        affich -no-log point
    date >> "$log_folder/$log_file"
    echo $ver >> "$log_folder/$log_file"
        affich -no-log point
    affich -no-log ok
fi


affich action "Déplacement dans /var/www "
if [ -d /var/www ]
then
        affich point
        affich point
        cd /var/www
        affich point
    affich ok
else
        affich point
    mkdir -p /var/www
    echo -ne "création réussie "
        affich point
    cd /var/www
        affich point
    affich ok
fi


affich action "Mise à jour de la liste des paquets "
    affich point
    affich point
sudo apt-get update >> "$log_folder/$log_file" 2>> "$log_folder/$log_error_file"
    affich point
affich ok

affich action "Installation de GIT "
if check_install "git";
then
        affich point
        affich point
        affich point
    affich already
else
    install git
        affich point
        affich point
    affich ok
fi

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
    affich already
else
    echo -ne "Aucun serveur web installé ! "
    affich ok
fi

if [ -z $webserver ]
then
    affich action "Installation de lighttpd "
    if ! install lighttpd ; then affich error ;fi
        affich point
        affich point
    affich ok

    affich action "Installation de PHP & SQLite "
    install php5
    install php5-cgi
    install sqlite
    install php5-sqlite
    install php5-curl
    /etc/init.d/lighttpd force-reload >> "$log_folder/$log_file" 2>> "$log_folder/$log_error_file"
        affich point
    affich ok

    affich action "Configuration de lighttpd "
    sudo lighttpd-enable-mod fastcgi-php >> "$log_folder/$log_file"
        affich point
    sudo lighttpd-enable-mod cgi >> "$log_folder/$log_file"
        affich point
    echo -e '$HTTP["url"] =~ "/cgi-bin/" {\n\tcgi.assign = ( "" => "" )\n}' >> /etc/lighttpd/conf-enabled/10-cgi.conf
        affich point
    sudo /etc/init.d/lighttpd force-reload >> "$log_folder/$log_file"
        affich point
    affich ok
fi

affich action "Clonage de Eva "
if [ -z $dev_mod ] || [ $dev_mod -ne 1 ]
then
    git -b $branche clone $url_git /var/www/EVA >> "$log_folder/$log_file" 2>> "$log_folder/$log_error_file"
else
    if [ ! -d /var/www/EVA ]
    then
        mkdir -p /var/www/EVA
    fi
fi
affich point
chmod -R 775 /var/www/EVA
affich point
affich point
affich ok

affich action "Création d'un utilisateur eva "
affich point
useradd --home /home/eva --groups web,eva eva >> "$log_folder/$log_file" 2>> "$log_folder/$log_error_file"
affich point
sudo -u eva cat /var/log/eva/install.log >> "$log_folder/$log_file" 2>>"$log_folder/$log_file"
affich point

if [ ! $? -eq 1 ]
then
    affich ok
else
    affich error
fi