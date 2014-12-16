<?php
/*
 @nom: Plugin GPIO
 @auteur: Thib3113 (thib3113@gmail.com)
 @description:  Plugin de gestion des GPIO
 */

if($myUser->is_connect){
    Plugins::addHook("header", "Configuration::addMenuItem", array("GPIO", "gpio","dot-circle-o", 1));
}

function affich_gpio(){
    global $RaspberryPi;

    var_dump($RaspberryPi->getTablePins());
    Configuration::setTemplateInfos(array("tpl" => __DIR__.'/vues/gpio/gpio.tpl'));
    Plugins::callHook("pre_header");
    Plugins::callHook("header");
    Plugins::callHook("pre_gpio");
    Plugins::callHook("gpio");
    Plugins::callHook("pre_footer");
    Plugins::callHook("footer");
}

function affich_json_gpio(){
    global $RaspberryPi;

    $ajaxResponse->set_response(array("status" => true, "message" => "ok", "dash_title" => "lorem", "dash_content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam aut sequi nobis corporis veniam voluptatem reiciendis animi necessitatibus fugit! At quos dolor iusto libero. Ullam reiciendis, soluta ea dolore distinctio. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet eaque neque quaerat voluptates obcaecati aspernatur, minima iure quas. Natus ea eius voluptates. Sed iure, iste omnis natus similique quidem fugit"));
}