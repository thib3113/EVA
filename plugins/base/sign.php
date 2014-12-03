<?php

function disconnect(){
    global $myUser;
    if(is_a($myUser, "User")){
    	$myUser->disconnect();
        Functions::redirect("index.php", "Vous allez être déconnecté !", 3);
    }
    // else

}

Plugins::addHook("signout", "disconnect");

Plugins::callHook("pre_signout");
Plugins::callHook("signout");