<?php
//on définis le root
define('ROOT', dirname(dirname(__DIR__)));
ob_start();
//on inclus les fichiers de base
require ROOT.'/base.php';


// var_dump($_SESSION);
// Fichier API
// 
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

$return = $API["BASIC_RETURN"];

//on déclare les fonctions basique de l'api
function API_GET_PING(&$return){
    $return = array(
        "status" => true,
        "ping" => "pong",
        "version" => PROGRAM_VERSION,
    );
    return $return;
}


function API_GET_GPIO_STATE(&$return){
    global $RaspberryPi, $myUser, $API;
    if($myUser->isConnect()){
        $return = array(
            "status" => true,
            "message" => "success",
            "error_code" => 200,
            "GPIO" => $RaspberryPi->getAllState(),
            );
    }
    else
        $return = $API["NEED_AUTH"];

    return $return;
}

function API_GET_CHECK_UPDATE(&$return){
    global $RaspberryPi, $myUser, $API;
        if($myUser->isConnect()){
            $RaspberryPi->checkUpdate();
            $return = array(
                "status" => true,
                "message" => "success",
                "error_code" => 200,
                );
        }
        else{
            $return = $API["NEED_AUTH"];
        }
}

function API_SET_GPIO_STATE(&$return){
        global $API, $myUser, $_;
        if(!$myUser->isConnect()){
            return $API["NEED_AUTH"];
        }
        if((empty($_["GPIO"]) && intval($_["GPIO"]) !== 0) || (empty($_["GPIO_STATE"]) && intval($_["GPIO_STATE"]) !== 0) ){
            $return = array(
                "status" => false,
                "message" => "parametre manquant",
                "error_code" => 400,
                );
        }
        elseif(!is_int((int)$_["GPIO_STATE"]) || !is_int((int)$_["GPIO"])){

            $return = array(
                "status" => false,
                "message" => "parametre incorrects",
                "error_code" => 405,
                );
        }
        else{
            $RaspberryPi->write($_["GPIO"], $_["GPIO_STATE"], true);

            $return = array(
                "status" => true,
                "message" => "success",
                "error_code" => 200,
                "wiringpin" => $_["GPIO"],
                "state" => $RaspberryPi->read($_["GPIO"]),
                );

        }
        return $return;
}

function API_SET_USER_INFO(&$return){
    global $API, $myUser, $_;
        if(!$myUser->isConnect()){
            $return = $API["NEED_AUTH"];
            break;
        }
        $myUser->setUsername($_["username"]);
        $myUser->setPass($_["pass"], $myUser->getUsername(), true);
        $myUser->setEmail($_["email"]);
        var_dump($myUser);
        // $myUser->sgdbSave();
        $return = array(
            "status" => true,
            "message" => "success",
            "error_code" => 200,
            );
        return $return;

}

function API_SET_AUTH(&$return){
    global $_, $myUser;
    if($myUser->is_connect){
        $return["message"] = "deja connecte";
        $return["error_code"] = 200;
        return;   
    }
    if(!empty($_['user']) && !empty($_['pass'])){
        $_['remember_me'] = empty($_['remember_me'])? false : $_['remember_me'];
        $myUser = new User(
            array(
                "ConnectionOptions" => array(
                    "expiration" => (!empty($_["expire"])? $_["expire"] : /*time()*/1 ),
                    "appInfos"  => (!empty($_SERVER["HTTP_X_APPINFO"])? $_SERVER["HTTP_X_APPINFO"] : "web|".$_SERVER["HTTP_USER_AGENT"] )
                )
            ) 
        );
        $myUser->connect(
            $_['user'],
            $_['pass'],
            $_['remember_me']
        );
        if($myUser->is_connect)
            $return = array("status" => true,  "error_code" => 200, "message" => "Vous êtes connectés");
        else
            $return = array("status" => false, "error_code" => 401, "message" => "Le nom d'utilisateur et/ou le mot de passe est incorrect");
    }
    else{
        $return["message"] = "parametre incorrects";
        $return["error_code"] = 400;
    }

}

Plugin::addHook("API_GET_PING", "API_GET_PING", array(&$return));
Plugin::addHook("API_SET_USER_INFO", "API_SET_USER_INFO", array(&$return));
Plugin::addHook("API_GET_GPIO_STATE", "API_GET_GPIO_STATE", array(&$return));
Plugin::addHook("API_GET_CHECK_UPDATE", "API_GET_CHECK_UPDATE", array(&$return));
Plugin::addHook("API_SET_GPIO_STATE", "API_SET_GPIO_STATE", array(&$return));
Plugin::addHook("API_SET_AUTH", "API_SET_AUTH", array(&$return));

//on appelle le hook demandé par le destinataire ( le hook fera les vérifications necessaires )
if(!empty($_["type"]) && !empty($_["API"])){
   Plugin::callHook("API_".strtoupper($_["type"])."_".$_["API"]);
}
else{
    $return["message"] = "parametre manquants";
    $return["error_code"] = 400;
}
die(json_encode($return));

// if(!empty($_GET['get'])){

    // switch ($_GET['get']) {
        // case "ping":
//         $return = array(
//             "status" => true,
//             "ping" => "pong",
//             );
//         echo json_encode($return);
//         break;
//         case "GPIO_STATE"
//         if($myUser->isConnect()){
//             $return = array(
//                 "status" => true,
//                 "message" => "success",
//                 "error_code" => 200,
//                 "GPIO" => $RaspberryPi->getAllState(),
//                 );
//         }
//         else{
//             $return = $API_NEED_AUTH;
//         }
//         break;
//         case "CHECK_UPDATE":

//         if($myUser->isConnect()){
//             $RaspberryPi->checkUpdate();
//             $return = array(
//                 "status" => true,
//                 "message" => "success",
//                 "error_code" => 200,
//                 );
//         }
//         else{
//             $return = $API_NEED_AUTH;
//         }
//         break;

//         default:
//             # code...
//         break;
//     }
//     echo json_encode($return);
    
// }
// elseif(!empty($_GET['set'])){

//     switch ($_GET['set']) {
//         case "GPIO_STATE":
//         if(!$myUser->isConnect()){
//             $return = $API_NEED_AUTH;
//             break;
//         }
//         if((empty($_GET["GPIO"]) && intval($_GET["GPIO"]) !== 0) || (empty($_GET["GPIO_STATE"]) && intval($_GET["GPIO_STATE"]) !== 0) ){
//             $return = array(
//                 "status" => false,
//                 "message" => "parametre manquant",
//                 "error_code" => 400,
//                 );
//         }
//         elseif(!is_int((int)$_GET["GPIO_STATE"]) || !is_int((int)$_GET["GPIO"])){

//             $return = array(
//                 "status" => false,
//                 "message" => "parametre incorrects",
//                 "error_code" => 405,
//                 );
//         }
//         else{
//             $RaspberryPi->write($_GET["GPIO"], $_GET["GPIO_STATE"], true);

//             $return = array(
//                 "status" => true,
//                 "message" => "success",
//                 "error_code" => 200,
//                 "wiringpin" => $_GET["GPIO"],
//                 "state" => $RaspberryPi->read($_GET["GPIO"]),
//                 );

//         }
//         break;
//         case "user_info":
//         if(!$myUser->isConnect()){
//             $return = $API_NEED_AUTH;
//             break;
//         }
//         $myUser->setusername($_["username"]);
//         $myUser->setPass($_["pass"], $myUser->getUsername(), true);
//         $myUser->setEmail($_["email"]);
//         var_dump($myUser);
//         // $myUser->sgdbSave();
//         $return = array(
//             "status" => true,
//             "message" => "success",
//             "error_code" => 200,
//             );
//         break;
//         case "app-update":
//         if(!$myUser->isConnect()){
//             $return = $API_NEED_AUTH;
//             break;
//         }
//         break;
//         default:
//             # code...
//         break;
//     }
    echo json_encode($return);
ob_end_flush();