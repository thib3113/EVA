<?php

Class Functions extends SgdbManager{


    public static function log($log, $label = "notice"){
        if(!is_file(ROOT.'/'.LOG_FILE)){
            self::createLogFile();
            self::log($log, $label);
        }
        else{
            $timestamp = date("r", time());
            $fp = fopen(ROOT.'/'.LOG_FILE, 'a+');
            if(!fwrite($fp, "$label : $timestamp : $log\n"))
                return false;
            else
                return true;
        }
            
    }

    public static function createLogFile(){
        if(!$fp = fopen(ROOT.'/'.LOG_FILE,"a+")) // ouverture du fichier en écriture
            return false;
        if(self::log("Création du fichier de log"))
            die('écriture du fichier de log impossible !');
        else
            chmod(ROOT.'/'.LOG_FILE, 0777);
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

    public static function getDebugList(){
        if(DEBUG && !Functions::isAjax()){
            //on écris les debug
            $list_debug = '<div id="debug_list">';
            foreach ($GLOBALS['debugItems'] as $value) {
                $list_debug .= $value;
            }
            $list_debug .= '</div>';
            return $list_debug;
        }
    }

    public static function getExecutionTime($start){
        $total = number_format(microtime(true)-$start,3);
        if(intval($total)>0)
            return "$total seconde".($total>1? "s" : "");
        else{
            $decimal = substr($total, strpos($total, '.')+1);
            return "$decimal milliseconde".($decimal>1? "s" : "");
        }
    }

    public static function randomStr($nbr) {
        $str = "";
        $chaine = "abcdefghijklmnpqrstuvwxyABCDEFGHIJKLMNOPQRSUTVWXYZ0123456789";
        $nb_chars = strlen($chaine);

        for($i=0; $i<$nbr; $i++)
        {
            $str .= $chaine[ rand(0, ($nb_chars-1)) ];
        }

        return $str;
    }

    public static function checkConnectivity($url = "http://www.google.com"){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,100); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        $curl_error = curl_errno($ch);
        if($curl_error>0)
            return false;
        else
            return true;
    }

    public static function getSupportedVersion(){
        
        $url = PROGRAM_WEBSITE."/json.php?get=supported_distribution"; //ne pas mettre http

        //on vérifie la connexion avec le site
        if(!self::checkConnectivity($url))
            return false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,1000); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        $json = json_decode($result, true);
        if($json['status']){
            $supported_version = $json['version'];
            return $supported_version;
        }

        return false;
    }

    public static function myVersionIsSupport(){
        $supported_versions = self::getSupportedVersion();
        $distribution = RaspberryPi::getInfos("distribution");
        $version = RaspberryPi::getInfos("version");

        foreach ($supported_versions as $version_support) {
            if(strtolower($version_support[0]) == strtolower($distribution))
                if(strtolower($version_support[1]) == strtolower($version))
                    return true;
        }
        return false;
    }

    public static function isApache(){
        return preg_match("~apache~Uis", $_SERVER['SERVER_SOFTWARE']);
    }
}