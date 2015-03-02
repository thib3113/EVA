<?php
define('TIME_START',microtime(true));
ini_set("log_errors", 1);
error_reporting(E_ALL);
set_error_handler("error_handler", E_ALL);
function error_handler( $errno , $errstr , $errfile , $errline, $errcontext){
	switch ($errno) {
		case 'E_ERROR':$erreur="E_ERROR";break;
		case 'E_WARNING':$erreur="E_WARNING";break;
		case 'E_PARSE':$erreur="E_PARSE";break;
		case 'E_NOTICE':$erreur="E_NOTICE";break;
		case 'E_CORE_ERROR':$erreur="E_CORE_ERROR";break;
		case 'E_CORE_WARNING':$erreur="E_CORE_WARNING";break;
		case 'E_COMPILE_ERROR':$erreur="E_COMPILE_ERROR";break;
		case 'E_COMPILE_WARNING':$erreur="E_COMPILE_WARNING";break;
		case 'E_USER_ERROR':$erreur="E_USER_ERROR";break;
		case 'E_USER_WARNING':$erreur="E_USER_WARNING";break;
		case 'E_USER_NOTICE':$erreur="E_USER_NOTICE";break;
		case 'E_STRICT':$erreur="E_STRICT";break;
		case 'E_RECOVERABLE_ERROR':$erreur="E_RECOVERABLE_ERROR";break;
		case 'E_DEPRECATED':$erreur="E_DEPRECATED";break;
		case 'E_USER_DEPRECATED':$erreur="E_USER_DEPRECATED";break;
		case 'E_ALL':$erreur="E_ALL";break;
		default: $erreur='E_UNKNOW';break;
	}
	// Functions::log("$erreur : $errstr FILE $errfile LINE $errline");
	return false;
}
@session_start();


require ROOT.'/config.php';
ini_set("error_log", LOG_FILE);
if(DEBUG){
    ini_set('display_errors', 'On');
}
else{
    ini_set("display_errors",0);
}

function autoload($name) {
	global $debugObject;
    if (file_exists(ROOT.'/classes/'.$name.".class.php")) {
        require_once(ROOT.'/classes/'.$name.".class.php");
        if(is_a($debugObject, "Debug"))
			$debugObject->addDebugList(array("timer" => "end load $name"));
    }
    else
        return false;
}


spl_autoload_register("autoload");
$debugObject = new Debug();
$debugObject->addDebugList(array("timer" => "autoload"));