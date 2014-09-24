<?php

$user = new User();

// var_dump($user);

if($user->connect($_['user'], $_['pass'], $_['remember_me']))
    echo json_encode(array("status" => "success","message" => "Vous êtes connectés"));
else
    echo json_encode(array("status" => "error","message" => "Le nom d'utilisateur et/ou le mot de passe est incorrect"));