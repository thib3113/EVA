<?php

Class Configuration extends ConfigurationManager{
	protected $id, $key, $value, $cacheConf;

    private static $templateInfos = array();
    private static $menu_items = array();
    private static $dashboard_list = array();
    private static $js_list = array(
        "start_head" => array(),
        "end_head" => array(),
        "start_body" => array(),
        "end_body" => array(),
    );
    private static $css_list = array();
    private static $DashboardWidgetList = array();
    private static $configs;

    function __construct(){
        global $RaspberryPi;
        self::$templateInfos = array(
            "tpl"          => ROOT.'/vues/index.tpl',
            "title"        => PROGRAM_NAME.' '.PROGRAM_VERSION,
            "menu_items"   => "",
            "distribution" => $RaspberryPi->getInfos("distribution"),
            "version"      => $RaspberryPi->getInfos("version"),
            );
        parent::__construct();
        self::fetch_config();
    }

    public function setKey($key){
        $this->key=$key;
    }

    public function addConfig($key, $value){
        $this->key=$key;
        $this->value=$value;
        $return = $this->sgdbSave();
        self::fetch_config();
        return $return;
    }

    public function getValue($key){
        return $this->value;
    }

    private function fetch_config(){
        $query = self::sgbdSelect();
        while($config = $query->fetch()){
            $return[$config["key"]] = $config["value"];
        }
        self::$configs = $return;
    }

    public function getConfigs(){
        return self::$configs;
    }

    public function getTemplateInfos(){
        self::$templateInfos['menu_items'] = self::triMenu();
        self::$templateInfos['externjs'] = self::$js_list;
        self::$templateInfos['externcss'] = self::$css_list;
        self::$templateInfos['configs'] = self::getConfigs();
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
        uasort (self::$menu_items , function($a,$b){
        if($a["position"]<0)
            $a["position"] = count(self::$menu_items)+$a["position"];
        if($b["position"]<0)
            $b["position"] = count(self::$menu_items)+$b["position"];

         return $a['position']>$b['position']?1:-1;
     });
        return self::$menu_items;
    }

    public static function addJS($url, $position = "end_body"){
        self::$js_list[$position][] = $url;
    }

    public static function addCSS($url){
        self::$css_list[] = $url;
    }

    public static function addDashboardWidget($title, $function, $position, $width = 4){
        if(!empty(self::$DashboardWidgetList[$title]))
            return false;
        
        self::$DashboardWidgetList[$title] = array(
                                                "function" => $function,
                                                "position" => $position,
                                                "width" => $width
                                            );
    }

    public static function getDashboardWidgetList(){
        return self::$DashboardWidgetList;
    }
}