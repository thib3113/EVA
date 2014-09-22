<?php
define('ROOT', __DIR__);
require ROOT.'/base.php';


if(empty($_['page']) || !is_file(ROOT.'/modeles/'.$_['page'])){
    Hook::callHook("pre_header");
    Hook::callHook("header");
    Hook::callHook("pre_index");
    Hook::callHook("index");
    Hook::callHook("pre_footer");
    Hook::callHook("footer");
}
else
    require ROOT.'/modeles/'.$_['page'];


$template_infos = $config->getTemplateInfos();

$smarty->assign("template_infos", $template_infos);


if(!is_file($template_infos['tpl']))
    $smarty->display(ROOT.'/vues/404.tpl');
else
    $smarty->display($template_infos['tpl']);
?>