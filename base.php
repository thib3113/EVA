<?php
require_once ROOT.DIRECTORY_SEPARATOR."root.php";


if(!is_file(DB_NAME) && basename($_SERVER['SCRIPT_FILENAME']) != "install.php"){
    header("location: install.php");
    die();
}

$smarty = new Smarty();
$smarty->template_dir = ROOT.'/cache/templates/';
$smarty->compile_dir = ROOT.'/cache/templates_c/';
$smarty->config_dir = ROOT.'/cache/configs/';
$smarty->cache_dir = ROOT.'/cache/cache/';

$debugObject = new Debug();
$system = new System();
$RaspberryPi = new RaspberryPi();
$config = new Configuration();
$myUser = new User;
$ajaxResponse = new Ajax();

//on liste les dossiers du dossier parent des plugins
$pluginsFolder_link = ROOT.DIRECTORY_SEPARATOR.PLUGIN_DIR;
$pluginsFolder = opendir($pluginsFolder_link) or Functions::fatal_error('Impossible d\'ouvrir le dossier des plugins');
while($folder = @readdir($pluginsFolder)) {
    //si ils ne correspondent pas aux actions linux
    if($folder != "." && $folder != ".."){
        $link = $pluginsFolder_link.DIRECTORY_SEPARATOR.$folder;
        //et que c'est bien un dossier
        if(is_dir($link)){
            //on liste les fichier du dossier en cours
            $folder = opendir($link) or Functions::fatal_error('Impossible d\'ouvrir le dossier plugin : '.$link);
            while($file = @readdir($folder)) {
              if(preg_match("~.plugin.~", $file)){
                $debugObject->addDebugList(array("plugins" => substr($link.DIRECTORY_SEPARATOR.$file, strlen(ROOT)+1) ));
                include $link.DIRECTORY_SEPARATOR.$file;
              }
            }
            closedir($folder);
        }
    }
}
closedir($pluginsFolder);



$_ = array_merge($_GET, $_POST);

$GLOBALS = array();

//on charge les plugins jquery de base
//Jquery
Configuration::addJs("//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js");
//bootstrap
Configuration::addJs("https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js");
//noty
Configuration::addJs("vues/js/jquery.noty.packaged.min.js");
//fonctions utiles
Configuration::addJs("vues/js/libs.js");
//debuguer perso
Configuration::addJs("vues/js/debug.js");

