<?php
define('ROOT', __DIR__);
session_start();
$start=microtime(true);
ini_set('display_errors','1');

error_reporting(E_ALL);

function __autoload($class_name){
    require_once ROOT.'/classes/'.$class_name . '.class.php';
}

require ROOT.'/config.php';

$config = new Configuration();
// $config->sgbdCreate();
// $config->setKey();
// $config->sgbdSave();

// $user_manager = new UsersManager();
// $user = $user_manager->isConnect();

$user = new User();
$user->sgbdCreate();

// var_dump(ROOT.LOG_FILE);
// var_dump(fileperms(ROOT.LOG_FILE));

if($user->isConnect()){
    Hook::callHook("pre_index_connect");
}
else
    Hook::callHook("pre_index_unconnect")
?>