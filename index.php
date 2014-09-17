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
$config->sgbdCreate();
?>