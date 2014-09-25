<?php
session_start();
$start=microtime(true);

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require ROOT.'/config.php';

function autoload($name) {  
    if (file_exists(ROOT.'/classes/'.$name.".class.php")) { 
        require_once(ROOT.'/classes/'.$name.".class.php"); 
    } 
    else
        return false;
} 

spl_autoload_register("autoload");

$config = new Configuration();

$smarty = new Smarty(); 
$smarty->template_dir = ROOT.'/cache/templates/';
$smarty->compile_dir = ROOT.'/cache/templates_c/';
$smarty->config_dir = ROOT.'/cache/configs/';
$smarty->cache_dir = ROOT.'/cache/cache/';


$user_manager = new UsersManager(array("table_users" => DB_PREFIX."Users"));
$user = $user_manager->isConnect();

$_ = array_merge($_GET, $_POST);

$GLOBALS['debugItems'] = array();

global $user,$config,$_;

if(Functions::isAjax()){
    require ROOT."/modeles/ajax.php";    
    Plugin::callHook("ajax");
    die();

}
else{
    //on charge toutes les fonctions de base
    if($user){
        Plugin::addHook("header", "Configuration::addMenuItem", array("Deconnexion", "home","home", 9999));   
    }
    else{
        Configuration::setTemplateInfos(array("tpl" => ROOT.'/vues/signin.tpl'));
        Configuration::addJs("vues/js/jquery.noty.packaged.min.js");
    }
}
