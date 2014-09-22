<?php

Class SgdbManager extends PDO{
    private $debugItem = array();

	function __construct(){
        if(!is_file(ROOT.DB_NAME))
            fopen(ROOT.DB_NAME, "a+");

        if(strtoupper(DB_TYPE) == "SQLITE")
            $db_type = "sqlite";
        else
            $db_type = "mysql";
        parent::__construct($db_type.':'.ROOT.DB_NAME);
	}

    function __destruct(){

        //on écris les debug
        $list_debug = '<div id="debug_list">';
        foreach ($this->debugItem as $value) {
            $list_debug .= $value;
        }
        $list_debug .= '</div>';
        echo $list_debug;
    }

    function sgbdType($type){
        switch($type){
            case "boolean":
                $DBType = "INT(1)";
            break;
            case "bigint":
                $DBType = "bigint(20)";
            break;
            case "int":
                $DBType = "int(10)";
            break;
            case 'longstring':
                $return = 'longtext';
            break;
            case 'key':
                $return = 'INTEGER NOT NULL PRIMARY KEY';
            break;
            case "timestamp":
            case "string":
                $DBType = "VARCHAR(255)";
            break;
            default:
                $DBType = "TEXT";
            break;
        }
    }

    public function debug($query, $params, $errorInfo, $file, $line){
        foreach ($errorInfo as $value) {
            if(!empty($value) )
                $i = 1;
        }
        $this->debugItem[] = '<div class="debug_content">
                                        <span class="debug_query">'.$query.'</span>
                                        <span class="debug_params">'.implode(",", $params).'</span>
                                        <span class="debug_error">'.(isset($i)? implode(",", $errorInfo) : "NULL" ).'</span>
                                        <span class="debug_file">'.$file.'</span>
                                        <span class="debug_line">'.$line.'</span>
            </div>';
    }

    public function _query($query, $params, $line, $file){
        if(!is_array($params))
            $params = array($params);

        if(DEBUG){
            self::debug($query, $params, $this->errorInfo(), $file, $line);
        }

        if(!$request = $this->prepare($query)){
            $this->sgdbError($query, $params, $this->errorInfo(), __FILE__, __LINE__);
        }
        else{
            $request->execute($params);
            return $request;
        }
    }

    public function sgdbError($query, $params, $error, $file, $line){
        Functions::log("Requete : ".$query." ( ".implode(",", $params)." ), return : ".implode(',',$error)." IN FILE ".$file." LINE ".$line, "ERROR");
        if(DEBUG){
            self::debug($query, $params, $this->errorInfo, $file, $line);
            exit();
        }
        else
            exit("Critical SQL error, see logs");
    }

	public function sgbdCreate(){
		$query = 'CREATE TABLE IF NOT EXISTS `'.DB_PREFIX.$this->TABLE_NAME.'` (';

		$i=0;
		foreach($this->object_fields as $field=>$type){
			$query .=($i>0?',' : '').'`'.$field.'`  '. $this->sgbdType($type).'  NOT NULL';
			$i++;
		}

		$query .= ');';
		$this->_query($query, __LINE__, __FILE__);
	}

    public function sgbdDrop(){
        $query = 'DROP TABLE `'.DB_PREFIX.$this->TABLE_NAME.'`;';
        
        $this->_query($query, __LINE__, __FILE__);
    }

    public function sgbdSave(){
        if(!empty($this->id)){
            $query = 'UPDATE `'.DB_PREFIX.$this->TABLE_NAME.'` ';
            $query .= 'SET ';
            $i =0;
            foreach($this->object_fields as $field=>$type){
                $id = eval('return htmlentities($this->'.$field.');');
                var_dump($id);
                $id = htmlentities($this->$field);
                var_dump($id);
                $query .= ($i>0?',' : '').'`'.$field.'`="'.$id.'"';
                $i++;
            }
        }
        else{
            $query = 'INSERT INTO `'.DB_PREFIX.$this->TABLE_NAME.'`(';
            $i=0;
            foreach($this->object_fields as $field=>$type){
                if($type!='key'){
                    $query .= ($i>0?',' : '').'`'.$field.'`';
                    $i++;
                }
            }
            $query .=')VALUES(';
            $i=0;
            foreach($this->object_fields as $field=>$type){
                if($type!='key'){
                    $query .= ($i>0?',' : '').'"'.eval('return htmlentities($this->'.$field.');').'"';
                    $i++;
                    var_dump(eval('return htmlentities($this->'.$field.');'));
                    var_dump(htmlentities($this->$field));
                }
            }

            $query .=');';
        }

    }

    public function exist_table($table, $autocreate = false){
        if(strtoupper(DB_TYPE) == "SQLITE")
            $query = 'SELECT COUNT(*) as count FROM sqlite_master WHERE type=\'table\' AND name=?';
        else
            $query = 'SELECT COUNT(*) as count FROM sqlite_master WHERE type=\'table\' AND name=?';
        
        $params = array(DB_PREFIX.$table);  
        $statement = self::_query($query,$params, __LINE__, __FILE__);
        if($statement!=false){
            $result = $statement->fetch();
            if($result['count']>0){
                return true;
            }
        }
        else{
            if($autocreate) 
                if($this->create())
                    return true;
            return false;
        }
    }

}