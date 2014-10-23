<?php
//on définis le root
define('ROOT', __DIR__);
//on inclus les fichiers de base
require ROOT.'/base.php';

//on inclus le modèle ou l'index
if(empty($_['page']) || !is_file(ROOT.'/modeles/'.$_['page'])){
    require ROOT.'/modeles/index.php';
}
else
    require ROOT.'/modeles/'.$_['page'].'.php';

//on ajoute le temps d'éxécution aux informations de template

$config->setTemplateInfos(array("executionTime" => Functions::getExecutionTime()));


$debugObject->addBasicDebug();

$config->setTemplateInfos(array("debugList" => $debugObject->getDebugList()));

//on récupère les informations du template définie précédemment
$template_infos = $config->getTemplateInfos();

//on les passe à smarty
$smarty->assign("template_infos", $template_infos);


$a = array("lsfjqslkjf", 125, 1.265, $smarty, array("a", 123), base64_encode("bouh!"), serialize(array("a", 12)) );
foreach ($a as $key => $value) {
    echo $debugObject->var_dump($value);
}


//on regarde si la page tpl existe
if(!is_file($template_infos['tpl'])){
    $smarty->display(ROOT.'/vues/404.tpl');
}
else{
    $smarty->display($template_infos['tpl']);
}
?>