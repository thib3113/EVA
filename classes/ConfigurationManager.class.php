<?php

Class ConfigurationManager extends SgdbManager{
	protected $id, $key, $value, $cacheConf;
	protected $TABLE_NAME = "configuration";
	protected $object_fields= array(
									'id'=>'key',
									'key'=>'string',
									'value'=>'string'
									);
	
    public function addConfig($key, $value){
        $this->key=$key;
        $this->value=$value;
        $return = $this->sgbdSave();
        return $return;
    }
}