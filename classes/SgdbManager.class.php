<?php

Class SgdbManager{
    private static $db;

	function __construct(){

        if(strtoupper(DB_TYPE) == "SQLITE")
            $db_type = "sqlite";
        else
            $db_type = "mysql";

        self::$db = new PDO($db_type.':'.DB_NAME);
        self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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
        global $debugObject;

        $infos = $debugObject->whoCallMe(2);

        if($errorInfo[0] == 0)
            $status = '<span class="label label-success">'.$errorInfo[0].'</span>';
        else
            $status = '<span class="label label-danger">'.$errorInfo[0].' ( '.$errorInfo[1].' ) : '.$errorInfo[2].'</span>';

        $list = '<kbd>'.self::boundQuery(self::$db, $query, $params).'</kbd>&nbsp;'.$status.' ON '.((!empty($file) && !empty($line))? $file.' LINE '.$line : $infos['file'].' LINE '.$infos['line']);
        $debugObject->addDebugList(array("SQL" => $list));
    }

    public static function _query($query, $params, $file, $line){
        if(!empty($params)){
            if(!is_array($params))
                $params = array($params);

            //on prépare la requete
            $request = self::$db->prepare($query);

                //si la requete n'as pas pu être préparé, on s'arrete là
                if($request){
                    $paramsTemp = array();
                    $i = 1;
                    //on choisis le bon type de protection en fonction du type de paramètre
                    foreach ($params as $param) {
                        switch (gettype($param)) {
                            case 'boolean':
                                $typeOfFormat = PDO::PARAM_BOOL;
                            break;
                            case 'integer':
                                $typeOfFormat = PDO::PARAM_INT;
                            break;
                            case 'array':
                                $param = serialize($param);
                                $typeOfFormat = PDO::PARAM_STR;
                            break;
                            case 'null':
                                $typeOfFormat = PDO::PARAM_NULL;
                            break;
                            break;
                            case 'string':
                            default:
                                $typeOfFormat = PDO::PARAM_STR;
                            break;
                        }
                        //on protège
                        $request->bindValue($i++, $param, $typeOfFormat);
                }
            }

            if(!$request){
                self::sgdbError($query, $params, self::$db->errorInfo(), $file, $line);
            }
            else{
                $request->execute($params);
                $return = $request;
            }
        }
        else{
            $result = self::$db->query($query);
            if($result === false){
                self::sgdbError($query, null, self::$db->errorInfo(), $file, $line);
                return false;
            }
            else
                $return = $result;
        }

        self::debug($query, $params, self::$db->errorInfo(), $file, $line);

        //return peux valoir 0 si aucune ligne n'est affectée
        if($return instanceof PDO)
            if($return->errorCode() !== "00000")
                self::sgdbError($query, $params, self::$db->errorInfo(), $file, $line);

        return $return;
    }

    private static function boundQuery($db, $query, $values) {
        return preg_replace_callback(
            '#\\?#',
            // Notice the &$values - here, we want to modify it.
            function($match) use ($db, &$values) {
                if (empty($values)) {
                    throw new PDOException('not enough values for query');
                }
                $value  = array_shift($values);

                // Handle special cases: do not quote numbers, booleans, or NULL.
                if (is_null($value)) return 'NULL';
                if (true === $value) return 'true';
                if (false === $value) return 'false';
                if (is_numeric($value)) return $value;

                // Handle default case with $db charset
                return $db->quote($value);
            },
            $query
        );
    }

    public static function sgdbError($query, $params, $error, $file, $line){
        global $smarty, $debugObject;
        Functions::log("Requete : ".$query." ".(empty($params)? "": "( ".implode(",", $params)." )" ).", return : ".implode(',',$error)." IN FILE ".$file." LINE ".$line, "ERROR");
        if(DEBUG)
            self::debug($query, $params, self::$db->errorInfo(), $file, $line);

        foreach (self::$db->errorInfo() as $value) {
            if(!empty($value) )
                $i = 1;
        }

        $smarty->assign('debugList', $debugObject->getDebugList());
        $smarty->assign("errorInfos", array(
            "query" => $query,
            "params" => (!empty($params)? implode(', ', $params) : ""),
            "error" => (isset($i)? self::$db->errorInfo() : "NULL" ),
            "file" => $file,
            "line" => $line,
            "bound" => self::boundQuery(self::$db, $query, $params)
            )
        );
        $smarty->display(ROOT.'/vues/SQLerror.tpl');
        die();
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

        return self::existTable();
	}

    // public function sgbdDrop($file = NULL, $line = NULL){
    //     //debug
    //     $debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
    //     $debug = $debug[0];

    //     $query = 'DROP TABLE `'.DB_PREFIX.$this->TABLE_NAME.'`;';
        
    //     $this->_query($query, (!empty($file)?$file:$debug['file']), (!empty($line)?$line:$debug['line']) );
    // }

    public function save($input, $optionnalParams=NULL, $file = NULL, $line = NULL){
        $debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $debug = $debug[0];
        if(empty($input))
            return false;

        $query = 'UPDATE '.DB_PREFIX.$this->TABLE_NAME.' SET '.$input.'='.self::$db->quote($this->$input).' WHERE ';
        if(!empty($optionnalParams))
            $query .= $optionnalParams.'='.self::$db->quote($this->$optionnalParams);
        else
            $query .= 'id='.$this->id;
        // $params = array($input => $this->$input, 'id' => $this->id);
        self::_query($query, null, (!empty($file)?$file:$debug['file']), (!empty($line)?$line:$debug['line']) );

    }

    public function sgbdSave( $file = NULL, $line = NULL){
        $debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $debug = $debug[0];
        $params = array();
        if(!empty($this->id)){
            $query = 'UPDATE `'.DB_PREFIX.$this->TABLE_NAME.'` ';
            $query .= 'SET ';
            $i =0;
            foreach($this->object_fields as $field=>$value){
                if($field != "id"){
                    $params[] = $this->$field;
                    $query .= ($i>0?',' : '').'`'.$field.'`=?';
                    $i++;
                }
            }
            $query .= ' WHERE id='.$this->id;
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
        return self::_query($query, $params, (!empty($file)?$file:$debug['file']), (!empty($line)?$line:$debug['line']) );

    }

    public function sgbdSelect(array $cols = null, array $where =null, $table = null, array $order =null, array $group_by =null, array $limit =null, $file=NULL, $line=NULL){
        //préparation du debug
        $debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $debug = $debug[0];

        $cols = (!empty($cols))? implode(", ", $cols) : '*';
        $table = (empty($table))? DB_PREFIX.$this->TABLE_NAME : DB_PREFIX.$table;
        $params = array();
        $where_temp = null;
        if(!empty($where)){
            $where_temp = ' WHERE ';
            $i=0;
            foreach ($where as $key => $value) {
                $where_temp .= ($i>0)? ' AND ' : "";
                $where_temp .= '`'.$key.'`=?';
                $params[] = $value;
                $i++;
            }
        }

        $order = (!empty($order))?' ORDER BY `'.implode("`, ", $order).'`' : '';
        $group_by = (!empty($group_by))?' GROUP BY `'.implode("`, `", $group_by).'`' : '';
        $limit = (!empty($limit))?' LIMIT `'.implode("`, `", $limit).'`' : '';


        $query = "SELECT $cols FROM ".$table.$where_temp.$order.$group_by.$limit;
        $return = self::_query($query, $params, (!empty($file)?$file:$debug['file']), (!empty($line)?$line:$debug['line']) );
        return $return;

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

        $statement = self::_query($query,$params, (!empty($file)?$file:$debug['file']), (!empty($line)?$line:$debug['line']) );
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