<?php

Class Configuration extends SgdbManager{
	protected $id, $key, $value, $cacheConf;
	protected $TABLE_NAME = "configuration";
	protected $object_fields= array(
									'id'=>'key',
									'key'=>'longstring',
									'value'=>'longstring'
									);

    private $templateInfos = array();

    function __construct(){
        $this->templateInfos = array(
            "tpl" => ROOT.'/vues/index.tpl',
            "title" => PROGRAM_NAME.' '.PROGRAM_VERSION,
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
        return $this->templateInfos;
    }

    public function setTemplateInfos($infos){
        foreach ($infos as $key => $value) {
            $this->templateInfos[$key] = $value;
        }
    }

}