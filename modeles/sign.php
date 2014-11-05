<?php

function disconnect(){
    global $myUser;

    // if(is_a($myUser, "User")){
    	Functions::redirect("index.php");
    	// $myUser->disconnect();
    // }
    // else

}

Plugin::addHook("signout", "disconnect");

Plugin::callHook("pre_signout");
Plugin::callHook("signout");