<?php

Class Configuration extends SQLite{
	protected $id, $key, $value;
	protected $TABLE_NAME = "configuration";
	protected $object_fields= array(
									'id'=>'key',
									'key'=>'longstring',
									'value'=>'longstring'
									);


}