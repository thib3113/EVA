<?php

function affich_sign($way = null){
    global $ajaxResponse, $_, $myUser;


    if(!empty($_['sign']))
        $way = $_['sign'];

    //si on arrive ici c'est que l'on as mis l'url
    if(!empty($way) && $way == "out" && !$myUser->is_connect)
        Functions::redirect("index.php",null, 0);

    if($myUser->is_connect){
        Plugin::callHook("pre_signout");
        Plugin::callHook("signout");
    }
    else{
        if(!empty($way) && $way == "in")
            Configuration::setTemplateInfos(array("tpl" => ROOT.'/vues/signin.tpl'));

        if(!empty($_['user']) && !empty($_['pass'])){
            $myUser = new User($_['user'], $_['pass'], $_['remember_me']);
            if($myUser->is_connect)
                $ajaxResponse->set_response(array("status" => true,"message" => "Vous êtes connectés"));
            else
                $ajaxResponse->set_response(array("status" => false,"message" => "Le nom d'utilisateur et/ou le mot de passe est incorrect"));
        }
    }

}



function disconnect(){
    global $myUser;
    if(is_a($myUser, "User")){
        $myUser->disconnect();
        Functions::redirect("index.php", "Vous allez être déconnecté !", 5);
    }
    // else

}

if($myUser->is_connect)
    Plugin::addHook("header", "Configuration::addMenuItem", array("Deconnexion", "sign","fa-times", -1, array("sign" => "out")));

Plugin::addHook("signout", "disconnect");
