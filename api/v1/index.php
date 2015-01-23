<?php
//on définis le root
define('ROOT', dirname(dirname(__DIR__)));
ob_start();
//on inclus les fichiers de base
require ROOT.'/base.php';

// Fichier API
$return = array(
"status" => false,
"message" => "Erreur inconnue",
"error_code" => 500,
);

//API sans auth
if(!empty($_GET['get'])){

    switch ($_GET['get']) {
        case "ping":
            $return = array(
                "status" => true,
                "ping" => "pong",
            );
            echo json_encode($return);
        break;
        default:
            # code...
        break;
    }
    
}


// API avec AUTH
if(!$myUser->is_connect){
    $return = array(
    "status" => false,
    "message" => "Vous devez être connecté pour accéder à l'API",
    "error_code" => 403,
    );
    die(json_encode($return));
}

if(!empty($_GET['get'])){

    switch ($_GET['get']) {
        case 'test':
             var_dump($RaspberryPi->readAll());
            break;
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
elseif(!empty($_GET['set'])){

    switch ($_GET['set']) {
        case "GPIO_STATE":
            if((empty($_GET["GPIO"]) && intval($_GET["GPIO"]) !== 0) || (empty($_GET["GPIO_STATE"]) && intval($_GET["GPIO_STATE"]) !== 0) ){
                $return = array(
                "status" => false,
                "message" => "parametre manquant",
                "error_code" => 400,
                );
            }
            elseif(!is_int((int)$_GET["GPIO_STATE"]) || !is_int((int)$_GET["GPIO"])){

                $return = array(
                "status" => false,
                "message" => "parametre incorrects",
                "error_code" => 405,
                );
            }
            else{
                $RaspberryPi->write($_GET["GPIO"], $_GET["GPIO_STATE"], true);

                $return = array(
                "status" => true,
                "message" => "success",
                "error_code" => 200,
                "wiringpin" => $_GET["GPIO"],
                "state" => $RaspberryPi->read($_GET["GPIO"]),
                );
                
            }
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