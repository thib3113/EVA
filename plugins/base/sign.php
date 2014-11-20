<?php

function disconnect(){
    global $myUser;
    if(is_a($myUser, "User")){
    	Functions::redirect("index.php", "Vous allez être déconnecté !", 3);
    	$myUser->disconnect();
    }
    // else

}

Plugin::addHook("signout", "disconnect");

Plugin::callHook("pre_signout");
Plugin::callHook("signout");