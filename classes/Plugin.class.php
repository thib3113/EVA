<?php

Class Plugin extends SgdbManager{
    static private $hook_list = array();
    static private $widget_list = array();
    static private $menuItems;

    function __construct(){
        parent::__construct();
    }

    static public function addHook($hook, $function, $args = array(), $widget_options = array()){
        self::$hook_list[$hook][] = array($function => $args);
        if(!empty($widget_options))
            self::$widget_list[$function] = $widget_options;
    }

    static public function callHook($hook){
        if(!empty(self::$hook_list[$hook])){
            foreach (self::$hook_list[$hook] as $select_hook) {
                call_user_func_array(key($select_hook), $select_hook[key($select_hook)]);
            }
        }
        else
            return false;
    }

    /**
     * Gets the value of hook_list.
     *
     * @return mixed
     */
    static public function getHookList()
    {
        return self::$hook_list;
    }

    /**
     * Gets the value of widget_list.
     *
     * @return mixed
     */
    static public function getWidgetList()
    {
        return self::$widget_list;
    }
}