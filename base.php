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


$user = new User();
$user->isConnect();

$_ = array_merge($_GET, $_POST);

$GLOBALS['debugItems'] = array();
$GLOBALS['menuItems'] = array();

global $user,$config,$_;

if(Functions::isAjax()){
    require ROOT."/modeles/ajax.php";    
    Plugin::callHook("ajax");
    die();

}
else{
    //on charge toutes les fonctions de base
    if($user){
        Plugin::addHook("header", "Configuration::addMenuItem", array("Accueil", "index","home", 0));   
        Plugin::addHook("header", "Configuration::addMenuItem", array("Deconnexion", "index","times", count($GLOBALS['menuItems'])+1, array("sign" => "out")));   
    }
    else{
        Configuration::setTemplateInfos(array("tpl" => ROOT.'/vues/signin.tpl'));
        Configuration::addJs("vues/js/jquery.noty.packaged.min.js");
    }
}
