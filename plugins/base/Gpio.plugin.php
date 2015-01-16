<?php
/*
 @nom: Plugin GPIO
 @auteur: Thib3113 (thib3113@gmail.com)
 @description:  Plugin de gestion des GPIO
 */

if($myUser->is_connect){
    Plugin::addHook("header", "Configuration::addMenuItem", array("GPIO", "gpio","dot-circle-o", 1));
}

function affich_gpio(){
    global $RaspberryPi, $smarty;

    $pins = array();
    $array_list_wiring_pin = $RaspberryPi->getListWiringPin();
    //on ajoute les etats
    foreach ($RaspberryPi->getTablePins() as $key => $value) {
        $pins[$key] = $value;
        if(!is_null($value['wiringPin']))
            $pins[$key]["state"] = $RaspberryPi->read($value['wiringPin']);
    }
    // var_dump($pins);
    $smarty->assign('pins', $pins);

    // var_dump($RaspberryPi->getListWiringPin());
    //test des led 
    // $led = new LED(array(array(0 , true), array(1 , false)));
    // $led1 = new LED(array(array(0 , false), array(1 , true)));

    // $led->power(true);
    // sleep(1);
    // $led->power(false);
    // $led1->power(true);
    // 
    // test des 7 segments
    // $sevenseg = new SevenSegment(array(), "max7219");

    // for ($i=0; $i < 20; $i++) {
    //         $sevenseg->affich($i);
    //     sleep(1);
    // }

    // $sevenseg->affich("hello");

    Configuration::setTemplateInfos(array("tpl" => __DIR__.'/vues/gpio/gpio.tpl'));
    Configuration::addJs('plugins/base/vues/gpio/js/gpio.js');
    Plugin::callHook("pre_header");
    Plugin::callHook("header");
    Plugin::callHook("pre_gpio");
    Plugin::callHook("gpio");
    Plugin::callHook("pre_footer");
    Plugin::callHook("footer");
}

function affich_json_gpio(){
    global $RaspberryPi;

    $ajaxResponse->set_response(array("status" => true, "message" => "ok", "dash_title" => "lorem", "dash_content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam aut sequi nobis corporis veniam voluptatem reiciendis animi necessitatibus fugit! At quos dolor iusto libero. Ullam reiciendis, soluta ea dolore distinctio. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet eaque neque quaerat voluptates obcaecati aspernatur, minima iure quas. Natus ea eius voluptates. Sed iure, iste omnis natus similique quidem fugit"));
}