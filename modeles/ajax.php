<?php

$GLOBALS['json_returns'] = array("status" => false, "message" => "unknow error");

if(!empty($_)){
    if(!empty($_['page'])){
        switch ($_['page']) {
            case 'signin':
                $user = new User();

                if($user->connect($_['user'], $_['pass'], $_['remember_me']))
                    $GLOBALS['json_returns'] = array("status" => "success","message" => "Vous êtes connectés");
                else
                    $GLOBALS['json_returns'] = array("status" => "error","message" => "Le nom d'utilisateur et/ou le mot de passe est incorrect");
            break;
            
            default:
                # code...
                break;
        }
        
    }
    


}



echo json_encode($GLOBALS['json_returns']);