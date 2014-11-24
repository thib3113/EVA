<?php
require_once ROOT.DIRECTORY_SEPARATOR."root.php";


if(!is_file(DB_NAME) && basename($_SERVER['SCRIPT_FILENAME']) != "install.php"){
    header("location: install.php");
    die();
}

$system = new System();
$debugObject = new Debug();

//on inclus tout les plugins
$pluginsFolder = ROOT.DIRECTORY_SEPARATOR.PLUGIN_DIR;
$pluginsFolder = opendir($pluginsFolder) or die('Erreur');
while($file = @readdir($pluginsFolder)) {
  if(preg_match("~.plugin.~", $file)){
    $debugObject->addDebugList(array("plugins" => $pluginsFolder.DIRECTORY_SEPARATOR.$file));
    include $pluginsFolder.DIRECTORY_SEPARATOR.$file;
  }
}
closedir($pluginsFolder);

$config = new Configuration();

$smarty = new Smarty(); 
$smarty->template_dir = ROOT.'/cache/templates/';
$smarty->compile_dir = ROOT.'/cache/templates_c/';
$smarty->config_dir = ROOT.'/cache/configs/';
$smarty->cache_dir = ROOT.'/cache/cache/';

$user = new User();
$myUser = $user->isConnect();

$_ = array_merge($_GET, $_POST);

$GLOBALS['menuItems'] = array();

if(Functions::isAjax()){
    require ROOT."/plugins/base/ajax.php";    
    Plugin::callHook("ajax");
    die();
}

//on charge toutes les fonctions de base
if($myUser){
    Plugin::addHook("header", "Configuration::addMenuItem", array("Accueil", "index","home", 0));   
    Plugin::addHook("header", "Configuration::addMenuItem", array("Deconnexion", "sign","times", count($GLOBALS['menuItems'])+1, array("sign" => "out")));
    Configuration::setTemplateInfos(array("tpl" => ROOT.'/vues/index.tpl'));
    Configuration::addJs('vues/js/jquery-ui.min.js');
    Configuration::addJs('vues/js/widget.js');
    Configuration::addJs('vues/js/dashboard.js');
    Configuration::addJs('vues/js/index.js');
}
else{
    Configuration::setTemplateInfos(array("tpl" => ROOT.'/vues/signin.tpl'));
}
    Configuration::addJs("vues/js/jquery.noty.packaged.min.js");

