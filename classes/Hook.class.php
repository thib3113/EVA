<?php

Class Hook extends SgdbManager{
    static private $hook_list = array();
    static private $menuItems;

    function __construct(){
        parent::__construct();
    }

    static public function addHook($hook, $function, $args){
        self::$hook_list[$hook][] = array($function => $args);
    }

    static public function callHook($hook){
        if(!empty(self::$hook_list[$hook])){
            foreach (self::$hook_list[$hook] as $select_hook) {
                if(!empty($select_hook[0]) && !empty($select_hook[1])){
                    return array_map($select_hook[0], $select_hook[1]);
                }
                else
                    return false;
            }
        }   
        else
            return false;
    }

    static public function addMenuItem($item_name, $icon, $params = null){
        self::$menuItems[] = array($item_name, $icon, $params);
    }

}