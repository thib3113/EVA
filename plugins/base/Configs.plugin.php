<?php
if($myUser->is_connect){
    function affich_configs(){
        global $smarty, $RaspberryPi, $system, $myUser;

        Configuration::setTemplateInfos(array("tpl" => __DIR__.'/vues/configs/configs.tpl'));
        Plugin::callHook("pre_header");
        Plugin::callHook("header");
        Plugin::callHook("pre_content");

        Configuration::addJs('vues/js/jquery-ui.min.js');
        Configuration::addJs("plugins/base/vues/configs/js/config.js");

        //accueil
        $nb_wiring_pi = count($RaspberryPi->getListWiringPin());

        $yourRaspberryPi = "Type : ".$RaspberryPi->getRaspVersion()." <br>
        Révision : ".$RaspberryPi->getInfos("revision").'<br>
        Nombre de pins : '.$RaspberryPi->getNumberOfPins().' et '.$RaspberryPi->getNumberOfOptionalPins().' pins optionnels <i title="non activable actuellement" class="fa fa-question-circle"></i> <br>
        dont '.$nb_wiring_pi.' pin'.($nb_wiring_pi>1? 's' : '').' controllable'.($nb_wiring_pi>1? 's' : '').' <br>
         <a href="http://fr.wikipedia.org/wiki/OS" title="Operating System">OS</a> : '.$RaspberryPi->getInfos('distribution', true).' '.$RaspberryPi->getInfos('version');
        $smarty->assign('yourRaspberryPi', $yourRaspberryPi);

        $smarty->assign('server_software', $_SERVER['SERVER_SOFTWARE']);
        $smarty->assign('phpversion', phpversion());

        //profil
        $smarty->assign("myUser", $myUser->getUserInfos());

        //plugins
        //TODO Faire fonctionner le serveur de plugins

        //mise à jour
        $erreur = array();
        // $smarty->assign("erreur_maj", $erreur);



        Plugin::callHook("content");
        Plugin::callHook("pre_footer");
        Plugin::callHook("footer");

    }

    Plugin::addHook("header", "Configuration::addMenuItem", array("Configuration", "configs","fa-cogs", -2));
}

function affich_json_configs(){
    if(!empty($_['check_update'])){
        switch ($_['check_update']) {
            case 'core':
                $lines = file($system->getUserSystemFolder().'/update_list.txt');
                $smarty->assign("update_list_system");
            break;
            
            default:
                # code...
                break;
        }
    }
}
