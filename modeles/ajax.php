<?php

$user = new User($_['user'], $_['pass'], $_['remember_me']);

if($user)
    echo json_encode(array("statut" => "success","message" => "Vous êtes connectés"));
else
    echo json_encode(array("statut" => "error","message" => "Le nom d'utilisateur et/ou le mot de passe est incorrect"));