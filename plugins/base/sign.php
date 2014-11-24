<?php

function disconnect(){
    global $myUser;
    if(is_a($myUser, "User")){
    	$myUser->disconnect();
        Functions::redirect("index.php", "Vous allez être déconnecté !", 3);
    }
    // else

}

Plugin::addHook("signout", "disconnect");

Plugin::callHook("pre_signout");
Plugin::callHook("signout");