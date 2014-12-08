<?php
if($myUser->is_connect){
    function affich_configs(){
        global $smarty, $RaspberryPi, $system;

        Configuration::setTemplateInfos(array("tpl" => ROOT.'/vues/configs.tpl'));
        Plugins::callHook("pre_header");
        Plugins::callHook("header");
        Plugins::callHook("pre_content");

        Configuration::addJs('vues/js/jquery-ui.min.js');
        Configuration::addJs("vues/js/config.js");
        $yourRaspberryPi = "Type : ".$RaspberryPi->getRaspVersion()." <br>Révision : ".$RaspberryPi->getInfos("revision").'<br> Nombre de pins : '.$RaspberryPi->getNumberOfPins().' et '.$RaspberryPi->getNumberOfOptionalPins().' pins optionnels <br> <a href="http://fr.wikipedia.org/wiki/OS" title="Operating System">OS</a> : '.$RaspberryPi->getInfos('distribution', true).' '.$RaspberryPi->getInfos('version');
        $smarty->assign('yourRaspberryPi', $yourRaspberryPi);

        $smarty->assign('server_software', $_SERVER['SERVER_SOFTWARE']);
        $smarty->assign('phpversion', phpversion());

        Plugins::callHook("content");
        Plugins::callHook("pre_footer");
        Plugins::callHook("footer");

    }

    Plugins::addHook("header", "Configuration::addMenuItem", array("Configuration", "configs","cogs", 0));
}