#! /bin/bash

GREEN="\\033[1;32m"
WHITE="\\033[0;39m"
RED="\\033[1;31m"
BLUE='\e[0;34m'
PURPLE='\e[0;35m'

affich_ok(){
    echo -e " [${GREEN}FAIT${WHITE}]"
}

affich_deja_fait(){
    echo -e " [${BLUE}DEJA FAIT${WHITE}]"
}

affich_erreur(){
    echo -e " [${RED}ERREUR${WHITE}]"
}

a_p(){
    echo -ne ". "
}

affich_action(){
    echo -ne "${PURPLE}- ${1}${WHITE}"
}

echo -e "${GREEN}   .~~.   .~~.   ${WHITE}             "
echo -e "${GREEN}  '. \ ' ' / .'  ${WHITE}       "
echo -e "${RED}   .~ .~~~..~.   ${WHITE}   _____ _   _  ___  "
echo -e "${RED}  : .~.'~'.~. :  ${WHITE}  |  ___| | | |/ _ \ "
echo -e "${RED} ~ (   ) (   ) ~ ${WHITE}  | |__ | | | / /_\ \\"
echo -e "${RED}( : '~'.~.'~' : )${WHITE}  |  __|| | | |  _  |"
echo -e "${RED} ~ .~ (   ) ~. ~ ${WHITE}  | |___\ \_/ / | | |"
echo -e "${RED}  (  : '~' :  )  ${WHITE}  \____/ \___/\_| |_/"
echo -e "${RED}   '~ .~~~. ~'   ${WHITE}                      "
echo -e "${RED}       '~'       ${WHITE}                      "
echo -e "Lien github : ${GREEN}https://github.com/thib3113/EVA${WHITE}"
echo -e "Lien du site : ${GREEN}http://evaproject.net/${WHITE}"
echo -e "En cas de problème, merci de joindre vos deux fichiers présent dans le dossier log avec votre problème"
echo -e "${GREEN}Crée par SEVERAC Thibaut étudiant${WHITE}\n"


affich_action "Création d'un dossier pour les logs "
if [ -d log ]
then
    a_p
    a_p
    a_p
    affich_deja_fait
else
    a_p
    mkdir log
    a_p
    a_p
    affich_ok
fi

affich_action "mise à jour de la liste des paquets "
a_p
sudo apt-get update >> log/install.log 2>> log/install.error.log
a_p
a_p
affich_ok

affich_action "Recherche d'un serveur web "
if [ $(sudo dpkg --get-selections | grep apache | grep -v deinstall | wc -l) -gt 0 ]
then
    webserver="apache"
fi

a_p

if [ $(sudo dpkg --get-selections | grep nginx | grep -v deinstall | wc -l) -gt 0 ]
then
    webserver="nginx"
fi

a_p

if [ $(sudo dpkg --get-selections | grep lighttpd | grep -v deinstall | wc -l) -gt 0 ]
then
    webserver="lighttpd"
fi

a_p

if [ ! -z $webserver ]
then
    echo -ne " ${webserver} est déjà installé !"
    affich_deja_fait
else
    echo " : Aucun serveur web installé, lighttpd sera installé !"
    affich_ok
fi

if [ -z $webserver ]
then
    affich_action "installation de lighttpd"
    sudo apt-get install -y lighttpd >> log/install.log 2>> log/install.error.log
    affich_ok

    affich_action "installation de PHP & SQLite"
    sudo apt-get install -y php5 php5-cgi php5-sqlite sqlite >> log/install.log 2>> log/install.error.log
    /etc/init.d/lighttpd force-reload >> log/install.log 2>> log/install.error.log
    affich_ok

    affich_action "Configuration de lighttpd"
    sudo lighttpd-enable-mod fastcgi-php >> log/install.log
    sudo lighttpd-enable-mod cgi >> log/install.log
    echo -e '$HTTP["url"] =~ "/cgi-bin/" {\n\tcgi.assign = ( "" => "" )\n}' >> /etc/lighttpd/conf-enabled/10-cgi.conf
    sudo /etc/init.d/lighttpd force-reload >> log/install.log
    affich_ok
else
    affich_erreur
    exit 1
fi
