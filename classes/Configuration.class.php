<?php

Class Configuration extends SgdbManager{
	protected $id, $key, $value, $cacheConf;
	protected $TABLE_NAME = "configuration";
	protected $object_fields= array(
									'id'=>'key',
									'key'=>'longstring',
									'value'=>'longstring'
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
        $this->sgbdSave();
    }

    public function getValue($key){
        return $this->value;
    }

    public function getConfig(){
        
    }

    public function getTemplateInfos(){
        self::$templateInfos['menu_items'] = self::triMenu();
        self::$templateInfos['js'] = self::$js_list;
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
        if(!empty($_['module']))
            if($_['module'] == $slug)
                $active = 1;

        self::$menu_items[] = array(
                                                    "name" => $name,
                                                    "link" => '?modele='.$slug.(!empty($params)? '&amp;'.implode('&amp;' ,$params) : ""), 
                                                    "icon" => $icon, 
                                                    "position" => $position, 
                                                    "params" => $params,
                                                    "active" => $active,
                                                    "custom_item" => (!empty($params['custom_item']) )? $name : ""
                                                    );
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