<?php
//on définis le root
define('ROOT', __DIR__);
ob_start();
//on inclus les fichiers de base
require ROOT.'/base.php';

//on inclus le modèle ou l'index
if(empty($_['page']) || !is_file(ROOT.'/plugins/base/'.$_['page'].'.php')){
    require ROOT.'/plugins/base/index.php';
}
else
    require ROOT.'/plugins/base/'.$_['page'].'.php';

//on ajoute le temps d'éxécution aux informations de template

$config->setTemplateInfos(array("executionTime" => Functions::getExecutionTime()));


$debugObject->addCustomQuery("SELECT * FROM ".DB_PREFIX."users WHERE id=1");
$debugObject->addBasicDebug();

$config->setTemplateInfos(array("debugList" => $debugObject->getDebugList()));

//on récupère les informations du template définie précédemment
$template_infos = $config->getTemplateInfos();

//on les passe à smarty
$smarty->assign("template_infos", $template_infos);

//on regarde si la page tpl existe
if(!is_file($template_infos['tpl'])){
    $smarty->display(ROOT.'/vues/404.tpl');
}
else{
    $smarty->display($template_infos['tpl']);
}

ob_end_flush();
?>