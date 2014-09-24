<?php

Class Functions extends SgdbManager{


    public static function log($log, $label = "notice"){
        if(!is_file(ROOT.LOG_FILE)){
            self::createLogFile();
            self::log($log, $label);
        }
        else{
            $timestamp = date("r", time());
            $fp = fopen(ROOT.LOG_FILE, 'a+');
            if(!fwrite($fp, "$label : $timestamp : $log\n"))
                return false;
            else
                return true;
        }
            
    }

    public static function createLogFile(){
        if(!$fp = fopen(ROOT.LOG_FILE,"a+")) // ouverture du fichier en écriture
            return false;
        if(self::log("Création du fichier de log"))
            die('écriture du fichier de log impossible !');
        else
            chmod(ROOT.LOG_FILE, 0777);
    }

    public static function slugIt($name) {
        /*
           Cleans the string given:
            - Removes all special caracters
            - Sets every string in lower case
            - Removes all similar caracters
        */
        
        $a = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ@()/[]|\'&';
        $b = 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn---------';
        $url = utf8_encode(strtr(utf8_decode($name), utf8_decode($a), utf8_decode($b)));
        $url = preg_replace('/ /', '-', $url);  
        $url = trim(preg_replace('/[^a-z|A-Z|0-9|-]/', '', strtolower($url)), '-');
        $url = preg_replace('/\-+/', '-', $url);
        $url = urlencode($url);

        return $url;
    }

    public static function getHttpResponseCode($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    public static function var_dump_advanced($text, $file, $line){
        $text = (!is_array($text))? array($text) : $text;
        echo "<pre>";
        echo "$file : $line";
        $b = debug_backtrace();
        var_dump($b[1]['function']);
        var_dump($b[1]['args'][0]);
        foreach ($text as $value) {
            var_dump($value);
        }
        echo "</pre>";
    }

    public static function isAjax(){
        $is_ajax = (array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')? true : false;
        return $is_ajax;
    }

    public static function echoDebugList(){
        if(DEBUG && !Functions::isAjax()){
            //on écris les debug
            $list_debug = '<div id="debug_list">';
            foreach ($GLOBALS['debugItems'] as $value) {
                $list_debug .= $value;
            }
            $list_debug .= '</div>';
            echo $list_debug;
        }
    }
}