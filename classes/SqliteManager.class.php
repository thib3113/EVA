<?php

Class SqliteManager extends SQLite3{

	function __construct(){
		$this->open(ROOT.DB_NAME);
	}

	function __destruct(){
		$this->close();
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

    function _query($query, $line, $file){
        if(!$this->exec($query))
            $this->sgdbError($query, $this->lastErrorMsg(), __FILE__, __LINE__);
        if(DEBUG){
            echo "Requete : $query ".(!empty($this->lastErrorMsg)? "return : ".$this->lastErrorMsg : "")." IN FILE $file LINE $line" ;
        }
    }

    function sgdbError($query, $error, $file, $line){
        Functions::log("Requete : ".$query.", return : ".$error." IN FILE ".$file." LINE ".$line, "ERROR");
        if(DEBUG)
            exit("Requete : ".$query.", return : ".$error." IN FILE ".$file." LINE ".$line);
        else
            exit("Critical SQL error, see logs");
    }

	public function sgbdClose(){
		$this->close();
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

    public function exist_table($table){
            $query = "SELECT name FROM sqlite_temp_master WHERE type='table'";
            // $query = 'SHOW TABLES LIKE '.self::escapeString($table);
            $exec = self::_query($query, __LINE__, __FILE__);
            var_dump($exec);
            return false;
            if($this->num_rows($exec) > 0)
                return true;
            else
                return false;
        }

    public function exist_cols($table, $cols){
        if($this->exist_table($table)){
            $query = 'SHOW COLUMNS FROM '.$table.' LIKE '.self::escapeString($cols);
            $exec = self::_query($query, __LINE__, __FILE__);
            if($this->num_rows($exec) > 0)
                return true;
            else
                return false;
        }
        else
            return false;
    }


}