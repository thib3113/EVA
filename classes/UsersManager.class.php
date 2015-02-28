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

    public function createUser($username, $pass, $email, $g_id = null, $Avatar = "" ,$PluginsList = array() ,$DashboardList = array()){
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
        if(!$this->setAvatar($Avatar))
            return false;
        if(!$this->setPluginsList($PluginsList))
            return false;
        if(!$this->setDashboardList($DashboardList))
            return false;
        if(!$this->sgdbSave())
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
        if(empty($username))
            return false;
        $this->username = $username;
        return true;
    }
    public function setPass($pass, $name = false, $need_encode = false){
        if(empty($pass))
            return false;
        if($need_encode && !$name)return false;
        $this->pass = $need_encode? Functions::preparePasswd($name, $pass) : $name;
        return true;
    }
    public function setEmail($email){
        if(empty($email))
            return false;
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
        return true;
    }


}