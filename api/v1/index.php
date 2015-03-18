<?php
//on définis le root
define('ROOT', dirname(dirname(__DIR__)));
//on inclus les fichiers de base
require ROOT.'/base.php';

$API["BASIC_RETURN"] = array(
    "status" => false,
    "message" => "Erreur inconnue",
    "error_code" => 500,
    );
$API["NEED_AUTH"] = array(
    "status" => false,
    "message" => "Vous devez être connecté pour accéder à l'API",
    "error_code" => 401,
    );

//si une application demande à changer l'expiration de son token
if($myUser->is_connect){
    if(!empty($_["expiration"])){
        $myUser->connection->setExpiration($_["expiration"]);
        $myUser->connection->sgdbSave();
    }
}

$return = $API["BASIC_RETURN"];

//on inclus les fonctions de l'api
require __DIR__."/basic_functions.php";

//on appelle le hook demandé par le destinataire ( le hook fera les vérifications necessaires )
if(!empty($_["type"]) && !empty($_["API"])){
   Plugin::callHook("API_".strtoupper($_["type"])."_".$_["API"]);
}
else{
    $return["message"] = "parametre manquants";
    $return["error_code"] = 400;
}

echo json_encode($return);
ob_end_flush();