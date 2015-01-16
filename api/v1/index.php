<?php
//on définis le root
define('ROOT', dirname(dirname(__DIR__)));
ob_start();
//on inclus les fichiers de base
require ROOT.'/base.php';

if(!$myUser->is_connect){
    $return = array(
    "status" => false,
    "message" => "Vous devez être connecté pour accéder à l'API",
    "error_code" => 403,
    );
    die(json_encode($return));
}

// Fichier API
$return = array(
"status" => false,
"message" => "Erreur inconnue",
"error_code" => 500,
);
if(!empty($_GET['get'])){

    switch ($_GET['get']) {
        case "GPIO_STATE":
            $return = array(
            "status" => true,
            "message" => "success",
            "error_code" => 200,
            "GPIO" => $RaspberryPi->getAllState(),
            );
        break;
        default:
            # code...
            break;
    }
    echo json_encode($return);
    
}
else{
    $return["message"] = "parametre incorrects";
    $return["error_code"] = 400;
    die(json_encode($return));
}