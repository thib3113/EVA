<?php

function gpioIndex(){
    
}

Plugin::addHook("gpio", "gpioIndex");

Plugin::callHook("pre_header");
Plugin::callHook("header");
Plugin::callHook("pre_gpio");
Plugin::callHook("gpio");
Plugin::callHook("pre_footer");
Plugin::callHook("footer");