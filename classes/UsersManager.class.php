<?php

class UsersManager extends SgdbManager{
    protected $id;
    protected $uid;
    protected $username;
    protected $pass;
    protected $group_id;
    protected $email;
    protected $create_time;
    protected $plugins_list;
    protected $dashboard_list;
    protected $avatar;
    protected $token;
    protected $TABLE_NAME = "users";
    protected $object_fields= array(
                                    'id'             => 'key',
                                    'username'       => 'string',
                                    'pass'           => 'string',
                                    'group_id'       => 'int',
                                    'email'          => 'longstring',
                                    'create_time'    => 'timestamp',
                                    'plugins_list'   => 'TEXT',
                                    'dashboard_list' => 'TEXT',
                                    'avatar'         => 'longstring',
                                    );

    private $default_g_id = 2;
    private $uid_table_name = "uid";
    private $current_uid = 0; //uid courant
    private $admin_g_id = 1;

    /**
     * Fonction de préparation du mot de passe
     * @param  string $username username de la personne
     * @param  string $pass     le motde pass en clair
     * @return string           le mot de passe hashé
     */
    private function preparePasswd($username, $pass){
        $hashKey = hash("adler32", $username);
        $pass_temp = $username.$hashKey.$pass;
        if(DB_HASH)
            $pass_hashed = hash(DB_HASH, $pass_temp);
        return $pass_hashed;
    }

    public function createUser($username, $pass, $email, $g_id = null){
        if(empty($g_id) )
            $g_id = $this->default_g_id;

        if(!$this->setUsername($username))
            return false;
        if(!$this->setPass($pass, $username, true))
            return false;
        if(!$this->setEmail($email))
            return false;
        if(!$this->setGroupId($g_id))
            return false;
        if(!$this->setCreateTime(time()))
            return false;
        if(!$this->setAvatar(""))
            return false;
        if(!$this->setToken())
            return false;
        if(!$this->setUid())
            return false;
        if(!$this->sgbdSave())
            return false;

        return true;
    }

    /**
     * Sets the value of id,.
     *
     * @param mixed $id, the id
     *
     * @return self
     */
    private function setId($id){
        $this->id = $id;
    }
    public function setusername($username){
        $this->username = $username;
        return true;
    }
    public function setPass($pass, $name = false, $need_encode = false){
        if($need_encode && !$name)return false;
        $this->pass = $need_encode? $this->preparePasswd($name, $pass) : $name;
        return true;
    }
    public function setEmail($email){
        $this->email = $email;
        return true;
    }
    public function setGroupId($group_id){
        $this->group_id = $group_id;
        return true;
    }
    public function setCreateTime($create_time){
        $this->create_time = $create_time;
        return true;
    }
    public function setAvatar($avatar){
        $this->avatar = $avatar;
        return true;
    }

    public function setPluginsList(array $plugins_list){
        $this->plugins_list = serialize($plugins_list);
        return true;
    }

    public function setDashboardList(array $dashboard_list){
        $this->dashboard_list = serialize($dashboard_list);
    }

    public function setToken(){
        $this->current_token = Functions::randomStr(rand(100, 127));
        $this->_query("UPDATE ".DB_PREFIX.$this->uid_table_name." SET token=?, time=?  WHERE uid=?", array($this->current_token, time(), $this->current_uid), __FILE__, __LINE__);

        return true;
    }
}