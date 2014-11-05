<?php

class System {
    private $user = ""; //nom de l'utilisateur système
    private $distrib = ""; //nom de la distribution utilisée
    private $distribVersion = ""; //numéro de version de la distribution utilisée
    private $systemUserAvailable = false; //regarde si l'utilisitateur système 

    public function __construct(){
        //on initialise les variables
        
        //on cherche l'utilisateur courant
        $this->setUser($this->getProcessUser());

        //on cherche la distrib courante
        $this->setDistrib($this->getCurrentDistrib());

        //on regarde si l'utilisateur systeme est installé
    }

    public function getProcessUser(){
        $processUser = posix_getpwuid(posix_geteuid());
        return $processUser['name'];

    }

    public function shell($str){
        exec($str, $return, $status);
        if(count($return) < 2 && $status == 0)
            $return = $return[0];
        else
            $return['status'] = $status;
        return $return;
    }

    public function getCurrentDistrib(){
        //on récupère le nom complet de la distrib
        $return = $this->shell("cat /etc/*-release | grep PRETTY_NAME= | cut -c14- | rev | cut -c2- | rev");

        if(preg_match("~Raspbian~Uis", $return))
            return "Raspbian";

        //si on ne reconnais pas la distribution 
        return $return;
    }

    public function getCurrentDistribVersion(){
        //on récupère la version de la distrib
        $return = $this->shell("uname -r"); 
        return $return;
    }

    //setter
    public function setUser($user){
        $this->user = $user;
    }
    public function setDistrib($distrib){
        $this->distrib = $distrib;
    }

    //getter
    public function getUser(){
        return $this->user;
    }
    public function getDistrib(){
        return $this->distrib;
    }
}