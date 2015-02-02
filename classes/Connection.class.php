<?php
Class Connection extends SgdbManager{
    protected $id;
    protected $uid;
    protected $token;
    protected $browser_infos;
    protected $ips;
    protected $time;
    protected $TABLE_NAME = "connect";
    protected $object_fields= array(
       'id'            => 'key',
       'uid'           => 'TEXT',
       'token'         => 'TEXT',
       'browser_infos' => 'TEXT',
       'ips'           => 'TEXT',
       'time'          => 'int',
       );

    public function setToken(){
        $this->current_token = Functions::randomStr(rand(100, 127));
        $this->_query("UPDATE ".DB_PREFIX.$this->uid_table_name." SET token=?, time=?  WHERE uid=?", array($this->current_token, time(), $this->current_uid), __FILE__, __LINE__);

        return true;
    }

    public function set_uid_infos(){
        if(!empty($this->current_uid)){
            $result = SgdbManager::sgbdSelect(array('*'), array("uid" => $this->current_uid), "uid", null, null, null,  __FILE__, __LINE__ );
            $result = $result->fetch();
        }
        else{
            $result = SgdbManager::sgbdSelect(array('*'), array("browser_infos" => $_SERVER['HTTP_USER_AGENT']), "uid", null, null, null,  __FILE__, __LINE__ );
            $result = $result->fetch();
            if(!$result){
                $this->generateUid();
                $result['uid'] = $this->current_uid;
                $result['token'] = "";
                $result['ips'] = serialize(array($_SERVER['REMOTE_ADDR']));
                $this->_query("INSERT INTO ".DB_PREFIX.$this->uid_table_name." (uid, browser_infos, ips, id, time) VALUES (?, ?, ?, ?, ?)", array($this->current_uid, $_SERVER['HTTP_USER_AGENT'], $result['ips'], $this->id, time() ), __FILE__, __LINE__);
            }
        }
        $this->current_ip = $_SERVER['REMOTE_ADDR'];
        $this->current_ip_list = Functions::secureUnserialize($result['ips']);
        $this->current_user_agent = $_SERVER['HTTP_USER_AGENT'];
        $this->current_uid = $result['uid'];
        $this->current_token = $result['token'];
        return true;
    }
}