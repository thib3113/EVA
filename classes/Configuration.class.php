<?php

Class Configuration extends SqliteManager{
	protected $id, $key, $value, $cacheConf;
	protected $TABLE_NAME = "configuration";
	protected $object_fields= array(
									'id'=>'key',
									'key'=>'longstring',
									'value'=>'longstring'
									);

    function __construct(){
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

}