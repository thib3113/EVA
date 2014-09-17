<?php

Class Functions extends SQLite{


    public static function log($log, $label = "notice"){
        if(!is_file(ROOT.LOG_FILE)){
            self::createLogFile();
            self::log($log, $label);
        }
        else{
            $timestamp = date("r", time());
            $fp = fopen(ROOT.LOG_FILE, 'a');
            if!(fwrite($fp, "$label : $timestamp : $log"))
                return false;
            else
                return true;
        }
            
    }

    public static function createLogFile(){
        $fp = fopen(ROOT.LOG_FILE,"a"); // ouverture du fichier en écriture
        if(self::log("Création du fichier de log"))
            die('écriture du fichier de log impossible !');
        else
            chmod(ROOT.LOG_FILE, 0777);
    }
}