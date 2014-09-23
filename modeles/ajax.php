<?php

function ajax_connect(&$user_manager, $user, $password, $remember_me = false){
    if($user_manager->connect($user, $password, $remember_me))
        echo json_encode(array("statut" => "success","message" => "Vous êtes connectés"));
    else
        echo json_encode(array("statut" => "error","message" => "Le nom d'utilisateur et/ou le mot de passe est incorrect"));
}