<?php

function affich_gpio(){
    // Plugins::addHook("gpio", "gpioIndex");

    Plugins::callHook("pre_header");
    Plugins::callHook("header");
    Plugins::callHook("pre_gpio");
    Plugins::callHook("gpio");
    Plugins::callHook("pre_footer");
    Plugins::callHook("footer");
}