<?php

Class SgdbManager{
    private static $db;

	function __construct(){
        // if(!is_file(ROOT.DB_NAME))
        //     fopen(ROOT.DB_NAME, "a+");

        if(strtoupper(DB_TYPE) == "SQLITE")
            $db_type = "sqlite";
        else
            $db_type = "mysql";

        self::$db = new PDO($db_type.':'.ROOT.DB_NAME);
	}

    function sgbdType($type){
        if(preg_match('~null_~Uis', $type)){
            $null = "";
            $type = preg_replace('~null_~Uis', '', $type);
        }
        else
            $null = " NOT NULL";

        switch($type){
            case "boolean":
                $DBType = "INT(1)".$null;
            break;
            case "bigint":
                $DBType = "bigint(20)".$null;
            break;
            case "int":
                $DBType = "int(10)".$null;
            break;
            case 'longstring':
                $DBType = 'longtext'.$null;
            break;
            case 'key':
                $DBType = 'INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL';
            break;
            case "timestamp":
            case "string":
                $DBType = "VARCHAR(255)".$null;
            break;
            default:
                $DBType = "TEXT".$null;
            break;
        }
        return $DBType;
    }

    public static function debug($query, $params, $errorInfo, $file, $line){

        foreach ($errorInfo as $value) {
            if(!empty($value) )
                $i = 1;
        }

        $GLOBALS['debugItems'][] = '<div class="debug_content">
                                        <span class="debug_query">'.$query.'</span>'.(empty($params)?'' :'
                                        <span class="debug_params">'.implode(",", $params).'</span>' ).'
                                        <span class="debug_error">'.(isset($i)? implode(",", $errorInfo) : "NULL" ).'</span>
                                        <span class="debug_file">'.$file.'</span>
                                        <span class="debug_line">'.$line.'</span>
            </div>';
    }

    public static function _query($query, $params, $file, $line){
        if(!empty($params)){
            if(!is_array($params))
                $params = array($params);

            $request = self::$db->prepare($query);

            self::debug($query, $params, self::$db->errorInfo(), $file, $line);

            if(!$request){
                self::sgdbError($query, $params, self::$db->errorInfo(), $file, $line);
            }
            else{
                $request->execute($params);
                return $request;
            }
        }
        else{
            $result = self::$db->exec($query);
            if($result === false){
                self::sgdbError($query, null, self::$db->errorInfo(), $file, $line);
            }
        }
    }

    public static function sgdbError($query, $params, $error, $file, $line){
        global $smarty;
        Functions::log("Requete : ".$query." ".(empty($params)? "": "( ".implode(",", $params)." )" ).", return : ".implode(',',$error)." IN FILE ".$file." LINE ".$line, "ERROR");
        if(DEBUG)
            self::debug($query, $params, self::$db->errorInfo(), $file, $line);

        foreach (self::$db->errorInfo() as $value) {
            if(!empty($value) )
                $i = 1;
        }
        $smarty->assign("errorInfos", array("query" => $query, "params" => (!empty($params)? implode(', ', $params) : "") , "error" => (isset($i)? self::$db->errorInfo() : "NULL" ), "file" => $file, "line" => $line));
        $smarty->display(ROOT.'/vues/SQLerror.tpl');
        // die();
    }

	public function sgbdCreate(){
		$query = 'CREATE TABLE IF NOT EXISTS `'.DB_PREFIX.$this->TABLE_NAME.'` (';

		$i=0;
		foreach($this->object_fields as $field=>$type){
			$query .=($i>0?',' : '').'`'.$field.'`  '. $this->sgbdType($type);
			$i++;
		}

        $query .= ');';

        self::_query($query, null, __FILE__, __LINE__);
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
                // var_dump($id);
                $id = htmlentities($this->$field);
                // var_dump($id);
                $query .= ($i>0?',' : '').'`'.$field.'`="'.$id.'"';
                $i++;
            }
        }
        else{
            $query = 'INSERT INTO `'.DB_PREFIX.$this->TABLE_NAME.'` (';
            $i=0;
            foreach($this->object_fields as $field=>$type){
                if($type!='key'){
                    $query .= ($i>0?',' : '').'`'.$field.'`';
                    $i++;
                }
            }
            $query .=') VALUES (';
            $i=0;
            foreach($this->object_fields as $field=>$type){
                if($type!='key'){
                    $query .= ($i>0?',' : '').'"'.eval('return htmlentities($this->'.$field.');').'"';
                    $i++;
                    // var_dump(eval('return htmlentities($this->'.$field.');'));
                    // var_dump(htmlentities($this->$field));
                }
            }

            $query .=');';
        }
        self::_query($query, null, __FILE__, __LINE__);

    }

    public function sgbdSelect($table, array $cols = null, array $where =null, array $order =null, array $group_by =null, array $limit =null, $file, $line){
        
        $cols = (!empty($cols))? implode("`, `, ", $cols) : '*';

        if(!empty($where)){
            $where_temp = 'WHERE ';
            $i=0;
            foreach ($where as $key => $value) {
                $where_temp .= ($i>0)? ' AND ' : "";
                $where_temp .= ''.$key.'=?';
                $i++;
            }
        }

        $order = (!empty($order))?'ORDER BY `'.implode("`, ", $order).'`' : '';
        $group_by = (!empty($group_by))?'GROUP BY `'.implode("`, `", $group_by).'`' : '';
        $limit = (!empty($limit))?'LIMIT `'.implode("`, `", $limit).'`' : '';
        $query = "SELECT $cols FROM $table $where_temp $order $group_by $limit";
        return self::_query($query, $where, $file, $line);
        
    } 

    public  function existTable($table = false, $autocreate = false){
        if(strtoupper(DB_TYPE) == "SQLITE")
            $query = 'SELECT COUNT(*) as count FROM sqlite_master WHERE type=\'table\' AND name=?';
        else
            $query = 'SELECT COUNT(*) as count FROM sqlite_master WHERE type=\'table\' AND name=?';
        if($table)
            $params = array($table);
        elseif(!empty($this->TABLE_NAME) )
            $params = array(DB_PREFIX.$this->TABLE_NAME);
        else
            return false;

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