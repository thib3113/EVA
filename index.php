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
    if(empty($_['page'])){
        $func_name = 'affich_'.$custom_function.'index';
        if(function_exists($func_name)){
            $func_name();
        }
        else
            Functions::fatal_error("impossible d'appeler la fonction $func_name");
    }
    elseif (!function_exists("affich_$custom_function".$_['page'])) {
        Functions::fatal_error("aucun template n'as était donné pour l'affichage");
    }
    else{
        $func_name = "affich_$custom_function".$_['page'];
        if(function_exists($func_name)){
            $func_name();
        }
        else
            Functions::fatal_error("impossible d'appeler la fonction $func_name");
    }
}
else
    affich_sign("in");

if(Functions::isAjax()){
    echo json_encode($ajaxResponse->get_response());
    die();
}

// $debugObject->addCustomQuery("SELECT * FROM ".DB_PREFIX."users WHERE id=1");
$debugObject->addBasicDebug();


//on ajoute le temps d'éxécution aux informations de template
$config->setTemplateInfos(array("executionTime" => Functions::getExecutionTime()));

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
Functions::logExecutionTime(Functions::getExecutionTime(false, true));

?>