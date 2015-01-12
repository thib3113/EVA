<?php

@session_start();

define('TIME_START',microtime(true));

require ROOT.'/config.php';
if(DEBUG){
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
else{
    ini_set("display_errors",0);
    error_reporting(0);
}

function autoload($name) {
    if (file_exists(ROOT.'/classes/'.$name.".class.php")) {
        require_once(ROOT.'/classes/'.$name.".class.php");
    }
    else
        return false;
}


spl_autoload_register("autoload");