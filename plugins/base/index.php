<?php

function widgetDefault(){
    
}

Plugins::callHook("pre_header");
Plugins::callHook("header");
Plugins::callHook("pre_content");
Plugins::callHook("content");
Plugins::callHook("pre_footer");
Plugins::callHook("footer");