<?php

Class PluginsManager extends SgdbManager{
    static private $hook_list = array();
    static private $menuItems;

    function __construct(){
        parent::__construct();
    }

    public function addHook($hook, $function, $args = array()){
        self::$hook_list[$hook][] = array($function => $args);
    }

    public function callHook($hook){
        if(!empty(self::$hook_list[$hook])){
            foreach (self::$hook_list[$hook] as $select_hook) {
                call_user_func_array(key($select_hook), $select_hook[key($select_hook)]);
            }
        }
        else
            return false;
    }
}