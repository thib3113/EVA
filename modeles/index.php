<?php

function widgetDefault(){
    
}

Plugin::callHook("pre_header");
Plugin::callHook("header");
Plugin::callHook("pre_content");
Plugin::callHook("content");
Plugin::callHook("pre_footer");
Plugin::callHook("footer");