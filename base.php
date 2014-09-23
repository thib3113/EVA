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


$user_manager = new UsersManager();
$user = $user_manager->isConnect();

$_ = array_merge($_GET, $_POST);

//on charge toutes les fonctions de base
if($user){
    Plugin::addHook("header", "Configuration::addMenuItem", array("Deconnexion", "home","home", 9999));   
}
else{
    Configuration::setTemplateInfos(array("tpl" => ROOT.'/vues/signin.tpl'));
    Plugin::addHook("header", "Configuration::addMenuItem", array('<p class="navbar-brand logo visible-*-*"> '.PROGRAM_NAME.' <i class="fa fa-github-alt fa-2x"></i> '.PROGRAM_VERSION.'</p>', "","", 0, array("custom_item" => 1))); 
}
