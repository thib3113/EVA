<?php
//on définis le root
define('ROOT', __DIR__);
ob_start();
//on inclus les fichiers de base
require ROOT.'/base.php';

$custom_function = "";
if(Functions::isAjax())
    $custom_function = "json_";

if($myUser->is_connect){
    //on inclus le modèle ou l'index
    if(empty($_['page']) || !function_exists("affich_$custom_function".$_['page'])){
        $func_name = 'affich_'.$custom_function.'index';
        $func_name();
    }
    else{
        $func_name = "affich_$custom_function".$_['page'];
        $func_name();
    }
}
else
    affich_sign("in");

if(Functions::isAjax()){
    echo json_encode($ajaxResponse->get_response());
    die();
}

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