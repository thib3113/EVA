<?php

Class Functions extends SgdbManager{

    private static $defaultRedirectText = "Redirection en cours";
    private static $defaultRedirectTime = 5;


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

    public static function removeRootPath($path){
        return substr($path, strlen(ROOT)+1);
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

    public static function getExecutionTime($short = false, $force_ms = false){
        $total_time = microtime(true)-TIME_START;
        if($force_ms)
            return $total_time;
        $total = number_format($total_time,3);
        if(intval($total)>0){
            if(!$short)
                return "$total seconde".($total>1? "s" : "");
            else
                return $total.'s'; 
        }
        else{
            $decimal = substr($total, strpos($total, '.')+1);
            if(!$short)
                return "$decimal milliseconde".($decimal>1? "s" : "");
            else
                return $decimal.'ms';
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
        global $RaspberryPi;
        $supported_versions = self::getSupportedVersion();
        $distribution = $RaspberryPi->getInfos("distribution");
        $version = $RaspberryPi->getInfos("version");

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

    public static function backupDb($db_name = DB_NAME){
        $i = 0;
        do{
            if(is_file(DB_NAME.'.'.time().".backup".($i>0?$i:"") )){
                $i++;
                $result = false;
            }
            else
                $result = true;
        }while(!$result);
        if(rename(DB_NAME, DB_NAME.'.'.time().".backup".($i>0?$i:""))){
            return DB_NAME.'.'.time().".backup".($i>0?$i:"");
        }
        else{
            return false;
        }
    }

    /**
     * Check if data is base64 encoded
     * @param  [type]  $data [description]
     * @return boolean       [description]
     */
    public static function isBase64Encoded($data)    {
        if (base64_encode(base64_decode($data, true)) === $data) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public static function isSerialized($data){
        if(@unserialize($data))
            return true;
        else 
            return false;
    }

    /**
     * Formatte des grands nombres
     * @source : http://www.metal3d.org/ticket/2009/02/18/php-les-grands-nombres-et-la-notation-scientifique;
     * @param  float $num          [description]
     * @param  string $floatsep     [description]
     * @param  string $thouthandsep [description]
     * @return [type]               [description]
     */
    public static function formatNumber($num, $floatsep=",", $thouthandsep=""){
        $float = null;
        if((int)$num != (float)$num ) $float = 2;
        return number_format($num,$float,$floatsep,$thouthandsep);
    }

    /**
     * redirige vers un endroit
     * @param  string $to   lien vers lequel on redirige
     * @param  string $text texte marqué
     * @param  int    $time Temps avant la redirection
     * @return page de redirection
     */
    public static function redirect($to, $text = null, $time = null){
        global $smarty, $debugObject, $config, $myUser;

        if($time === 0){
            header("location: $to");
            die("redirect 0s");
        }

        $text = (empty($text))? self::$defaultRedirectText : $text;
        $time = (empty($time))? self::$defaultRedirectTime : $time;


        Configuration::addJs("vues/js/redirect.js");

        $config->setTemplateInfos(array("debugList" => $debugObject->getDebugList()));
        $smarty->assign('template_infos', $config->getTemplateInfos());
        $smarty->assign("to", $to);
        $smarty->assign('time',$time);
        $smarty->assign("text", $text);
        $smarty->display(ROOT.'/vues/redirect.tpl');
        die();
    }

    public static function fatal_error($text, $file = null, $line = null){
        global $smarty, $debugObject;

        $infos = $debugObject->whoCallMe(1);
        $line = !empty($line)? $line : $infos['line'];
        $file = !empty($file)? $file : $infos['file'];

        $smarty->assign('debugList', $debugObject->getDebugList());
        $smarty->assign("errorInfos", array(
            "body" => $text,
            "file" => $file,
            "line" => $line,
            )
        );
        $smarty->display(ROOT.'/vues/fatal_error.tpl');
        die();
    }

    public static function list_plugins_active($dir){
        global $myUser;

        $liste_plugins =self::list_plugins($dir);
        $list_plugins_active = $myUser->getPluginsList();
        $return_list = array();

        foreach ($liste_plugins as $key => $plugins) {
            if(in_array($key, $list_plugins_active) || preg_match("~".DIRECTORY_SEPARATOR."base".DIRECTORY_SEPARATOR."~", substr($plugins, strlen(__DIR__))))
                $return_list[$key] = $plugins;
        }
        return $return_list;
    }

    public static function list_plugins($dir){
        global  $debugObject;
        
        $liste_link = array();
        //on liste les dossiers du dossier parent des plugins
        $pluginsFolder = opendir($dir) or Functions::fatal_error('Impossible d\'ouvrir le dossier des plugins');
        while($file = @readdir($pluginsFolder)) {
            //si ils ne correspondent pas aux actions linux
            if($file != "." && $file != ".."){
                $link = $dir.DIRECTORY_SEPARATOR.$file;
                //et que c'est bien un dossier
                if(is_dir($link)){
                    $liste_link = array_merge($liste_link, self::list_plugins($link));
                }
                else{
                    if(preg_match("~([A-Z][a-zA-Z_0-9]+)\\.plugin\\.php~", $file, $match)){
                        if(!empty($match[1])){
                            if(!class_exists($match[1])){
                                $debugObject->addDebugList(array("plugins" => substr($link, strlen(ROOT)+1) ));
                                $liste_link[$match[1]] = $link;
                                // $plugins->addPlugin(new $match[1]());
                            }
                        }
                    }
                }
            }
        }
        closedir($pluginsFolder);
        return $liste_link;
    }

    public static function secureUnserialize($linear){
        if(!self::isSerialized($linear))
            return array();
        else
            return unserialize($linear);
    }
    /**
     * [logExecutionTime description]
     * @param  [type] $time [description]
     * @return [type]       [description]
     */
    public static function logExecutionTime($time){
        global $system, $_;
        //on le lance en shell pour que ce ne soit pas php qui prenne du temps
        $system->shell('echo "'.time().':'.(!empty($_['page'])?$_['page']:"index").':'.escapeshellarg($time).'" >> '.ROOT.'/log/executionTime.txt');
    }
}