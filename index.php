<?php
define('ROOT', __DIR__);
require ROOT.'/base.php';


if(empty($_['page']) || !is_file(ROOT.'/modeles/'.$_['page'])){
    require ROOT.'/modeles/index.php';
}
else
    require ROOT.'/modeles/'.$_['page'].'.php';

$template_infos = $config->getTemplateInfos();

$smarty->assign("template_infos", $template_infos);


if(!is_file($template_infos['tpl'])){
    $smarty->assign('debugList',Functions::getDebugList());
    $smarty->display(ROOT.'/vues/404.tpl');
}
else{
    $smarty->assign('executionTime',Functions::getExecutionTime($start));
    $smarty->assign('debugList',Functions::getDebugList());
    $smarty->display($template_infos['tpl']);
}
?>