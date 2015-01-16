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
$plugins = new Plugin();
// $plugins = new Plugins();

//on liste les dossiers du dossier parent des plugins
$pluginsFolder_link = ROOT.DIRECTORY_SEPARATOR.PLUGIN_DIR;
$list_plugins = Functions::list_plugins_active($pluginsFolder_link);

foreach ($list_plugins as $key => $plugins) {
    include $plugins;
}


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

