<?php
define('ROOT', __DIR__);
session_start();
$start=microtime(true);

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$GLOBALS['debugItems'] = array();

require ROOT.'/config.php';

function autoload($name) {  
    if (file_exists(ROOT.'/classes/'.$name.".class.php")) { 
        require_once(ROOT.'/classes/'.$name.".class.php"); 
    } 
    else
        return false;
} 

spl_autoload_register("autoload");

$smarty = new Smarty(); 
$smarty->template_dir = ROOT.'/cache/templates/';
$smarty->compile_dir = ROOT.'/cache/templates_c/';
$smarty->config_dir = ROOT.'/cache/configs/';
$smarty->cache_dir = ROOT.'/cache/cache/';


echo '<meta charset="utf-8">';

$config = new Configuration();
$config->sgbdCreate();
echo "création de la base<br>";
$config->existTable();
$config->addConfig("test", "je suis un test");
echo "Remplissage de la base<br>";

$user = new User();
$user->sgbdCreate();
echo "création de la table User<br>";
$user->existTable();
echo "Remplissage de la table<br>";


Configuration::setTemplateInfos(array("tpl" => ROOT.'/vues/signin.tpl'));
$template_infos = $config->getTemplateInfos();
$smarty->assign("template_infos", $template_infos);
$smarty->assign('executionTime',number_format(microtime(true)-$start,3));
Functions::echoDebugList();
$smarty->display(ROOT."/vues/install.tpl");
Functions::echoDebugList();