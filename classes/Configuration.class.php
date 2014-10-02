<?php

Class Configuration extends SgdbManager{
	protected $id, $key, $value, $cacheConf;
	protected $TABLE_NAME = "configuration";
	protected $object_fields= array(
									'id'=>'key',
									'key'=>'string',
									'value'=>'string'
									);

    private static $templateInfos = array();
    private static $menu_items = array();
    private static $js_list = array();

    function __construct(){
        self::$templateInfos = array(
            "tpl" => ROOT.'/vues/index.tpl',
            "title" => PROGRAM_NAME.' '.PROGRAM_VERSION,
            "menu_items" => "",
        );

        parent::__construct();
    }

    public function setKey($key){
        $this->key=$key;
    }

    public function addConfig($key, $value){
        $this->key=$key;
        $this->value=$value;
        return $this->sgbdSave();
    }

    public function getValue($key){
        return $this->value;
    }

    public function getConfig(){
        
    }

    public function getTemplateInfos(){
        self::$templateInfos['menu_items'] = self::triMenu();
        self::$templateInfos['externjs'] = self::$js_list;
        return self::$templateInfos;
    }

    public static function setTemplateInfos($infos){
        foreach ($infos as $key => $value) {
            self::$templateInfos[$key] = $value;
        }
    }

    static public function addMenuItem($name, $slug, $icon, $position = null, $params = null){
        global $_;

        //on regarde si c'est la page active
        $active = 0;
        if(!empty($_['page'])){
            if($_['page'] == $slug)
                $active = 1;
        }
        else{
            if($slug == 'index')
                $active = 1;
        }

        $link = "?page=$slug";
        if(!empty($params)){
            foreach ($params as $key => $value) {
                $link .= "&amp;".urlencode($key).'='.urlencode($value);
            }
        }

        self::$menu_items[] = array(
                                                    "name" => $name,
                                                    "link" => $link, 
                                                    "icon" => $icon, 
                                                    "position" => $position, 
                                                    "params" => $params,
                                                    "active" => $active,
                                                    "custom_item" => (!empty($params['custom_item']) )? $name : ""
                                                    );
        $GLOBALS['menuItems'][] = self::$menu_items;
        return count(self::$menu_items)-1;
    }

    static public function addSubMenuItem($id, $name, $slug, $icon, $position = null, $params = null){
        self::$menu_items[$id]['sub_menu'][] = array(
                                                    "name" => $name,
                                                    "slug" => $slug, 
                                                    "icon" => $icon, 
                                                    "position" => $position, 
                                                    "params" => $params
                                                    );
    }

    static public function addDivider($id, $position){
        self::$menu_items[$id]['sub_menu'][] = array('divider' => 1, "position" => $position );
    }

    public static function triMenu(){
        uasort (self::$menu_items , function($a,$b){return $a['position']>$b['position']?1:-1;});
        return self::$menu_items;
    }

    public static function addJS($url){
        self::$js_list[] = $url; 
    }
}