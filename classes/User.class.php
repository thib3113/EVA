<?php


Class User extends SgdbManager{
    protected $id;
    protected $username;
    protected $forname;
    protected $lastname;
    protected $pass;
    protected $group_id;
    protected $email;
    protected $create_time;
    protected $plugins_list;
    protected $dashboard_list;
    protected $avatar;
    protected $TABLE_NAME = "users";
    protected $object_fields= array(
                                    'id'             => 'key',
                                    'username'       => 'string',
                                    'forname'       => 'string',
                                    'lastname'       => 'string',
                                    'pass'           => 'string',
                                    'group_id'       => 'int',
                                    'email'          => 'longstring',
                                    'create_time'    => 'timestamp',
                                    'plugins_list'   => 'TEXT',
                                    'dashboard_list' => 'TEXT',
                                    'avatar'         => 'longstring',
                                    );
    private         $appInfo = "webphp";
    
    //gravatar
    private         $gravatar_size = 80; // 1 - 2048 definie la taille de l'image
    private         $gravatar_default = "wavatar"; // image par défault de l'image [ 404 | mm | identicon | monsterid | wavatar ]
    private         $gravatar_max_rat = 'g'; // maximum rating ( aucune idée de à quoi ça sert )


    private         $admin_g_id     = 1;
    private         $enable_admin  = true; // active les admins
    public          $is_admin      = false; // l'utilisateur est il un administrateur
    public          $is_connect    = false; // l'utilisateur est il connecté
    
    private         $default_g_id  = 2;
    private         $col_groups  = "group_id"; //nom de la colonne du groupe
    private         $group_id_admin = 0; //valeur du groupe si l'utilisateur est admin
    
    private         $connectionOptions;

    function __construct(){

        foreach (debug_backtrace()[0]["args"] as $arg) {
            foreach ($arg as $key => $value) {
                if(method_exists($this,"set".ucfirst($key)))
                    $this->{"set".ucfirst($key)}($value);
            }
        }
        parent::__construct();

    }

    public function is_admin(){
        if($this->group_id == $this->admin_g_id){
            $this->is_admin = true;
            $GLOBALS['is_admin'] = true;
            return true;
        }
    }

    public function connect($user = null, $password = null, $rememberMe = false, $needEncrypt = true){
        $this->connection = new Connection($this->connectionOptions);
        $id = $this->connection->open($user, $password, $rememberMe, $needEncrypt);
        if($id !== false){
            $this->id = $id;
            $this->fillObject();
            // on génère les variables d'informations
            $GLOBALS['is_connect'] = true;
            $this->is_connect = true;
            $this->is_admin();
            return $this;
        }
    }

    public function disconnect(){
        $this->connection->close();
    }

    public function getUserInfos(){
        $id = $this->id;
        //création de la query
        $result_query = SgdbManager::sgbdSelect(array('*'), array("id" => $id), null, null,null,null,  __FILE__, __LINE__ );

        $return = $result_query->fetch();
        $return['avatar'] = empty($return['avatar'])? $this->getGravatar($return['email']) : $config['base_url'].'/vues/img_up/profils/'.$return['avatar'];
        return $return;

    }

    // public function createUser($username, $pass, $email, $g_id = null){
    //     if(empty($g_id) )
    //         $g_id = $this->default_g_id;

    //     if(!$this->setUsername($username))
    //         return false;
    //     if(!$this->setPass($pass, $username, true))
    //         return false;
    //     if(!$this->setEmail($email))
    //         return false;
    //     if(!$this->setGroupId($g_id))
    //         return false;
    //     if(!$this->setCreateTime(time()))
    //         return false;
    //     if(!$this->setAvatar(""))
    //         return false;
    //     if(!$this->sgdbSave())
    //         return false;

    //     return true;
    // }

    /**
     * Gets the value of id,.
     *
     * @return mixed
     */
    public function getId(){
        return $this->id;
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
        $this->plugins_list = $plugins_list;
    }

    public function setWidgetList(array $dashboard_list){
        $this->dashboard_list = $dashboard_list;
    }

    public function setWidgetSize($name, $new_size){
        $widget_list = $this->getWidgetList();
        foreach ($widget_list as $key => $value) {
            if($value["name"] == $name)
                $widget_list[$key]["width"] = $new_size;
        }
        $this->setWidgetList($widget_list);
    }

    public function addWidget($dashboard, $width){
        if($width>1)
            $width = $width*12;

        $widget = array("name" => $dashboard, "width" => $width);

        $currentDashboardList = $this->dashboard_list;
        
        $currentDashboardList[] = $widget;
        $this->setWidgetList($currentDashboardList);
    }

    public function getUsername(){
        return $this->username;
    }
    
    public function getPass(){
        return $this->pass;
    }

    public function getPluginsList(){
        return Functions::secureUnserialize($this->plugins_list);
    }

    public function getWidget($widget_name){
        $widget_list = $this->getWidgetList();
        foreach ($widget_list as $widget) {
            if($widget["name"] == $widget_name)
                return $widget;
        }
        return false;
    }

    public function getWidgetList(){
        $dashboard_list = array();
        if(!is_array($this->dashboard_list) && Functions::isSerialize($this->dashboard_list))
            $dashboard_list_temp = unserialize($this->dashboard_list);
        else
            $dashboard_list_temp = $this->dashboard_list;
        if($dashboard_list_temp){
            // uasort($dashboard_list_temp, function($a,$b){
                                                // if(!empty($a['position']) && !empty($b['position']) )
                                                //     return $a['position']>$b['position']?1:-1;
                                                // else
                                                //     return 0; 
                                                // });
            foreach ($dashboard_list_temp as $key => $value) {
                $dashboard_list[$key] = $value;
            }
        }
        // var_dump($dashboard_list);
        return $dashboard_list;
    }

    public function getAvatar(){
        return !empty($this->avatar)? $this->avatar : $this->getGravatar();
    }

    public function getName(){
        return !empty($this->lastname) && !empty($this->forname)? $this->forname." ".$this->lastname : $this->username;
    }

    public function getShortName(){
        return !empty($this->forname) ? $this->forname : $this->username;
    }

    /*******************
    
    Gestion du gravatar

    *********************/

    public function setGravatarDefault($default){
        $this->gravatar_default = $default;
    }

    public function setGravatarSize($size){
        $this->gravatar_size = $size;
    }


    public function setGravatarMaxRat($max_rat){
        $this->gravatar_max_rat = $max_rat;
    }

    public function getGravatar() {
        $email = $this->email;
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= '?s='.$this->gravatar_size.'&amp;d='.$this->gravatar_default.'&amp;r='.$this->gravatar_max_rat;
        if(1 == 1 || Functions::getHttpResponseCode($url) != 404)
            return $url;
        else{
            if($this->gravatar_default != "404" && $this->gravatar_default != "mm" && $this->gravatar_default != "identicon" && $this->gravatar_default != "monsterid" && $this->gravatar_default != "wavatar")
                return $url;
            else
                return false;
        }
    } 
    

    /**
     * Gets the value of group_id.
     *
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * Gets the value of email.
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Gets the value of create_time.
     *
     * @return mixed
     */
    public function getCreateTime()
    {
        return $this->create_time;
    }

    /**
     * Gets the value of appInfo.
     *
     * @return mixed
     */
    public function getAppInfo()
    {
        return $this->appInfo;
    }

    /**
     * Sets the value of appInfo.
     *
     * @param mixed $appInfo the app info
     *
     * @return self
     */
    private function setAppInfo($appInfo)
    {
        $this->appInfo = $appInfo;

        return $this;
    }

    /**
     * Gets the value of gravatar_size.
     *
     * @return mixed
     */
    public function getGravatarSize()
    {
        return $this->gravatar_size;
    }

    /**
     * Gets the value of gravatar_default.
     *
     * @return mixed
     */
    public function getGravatarDefault()
    {
        return $this->gravatar_default;
    }

    /**
     * Gets the value of gravatar_max_rat.
     *
     * @return mixed
     */
    public function getGravatarMaxRat()
    {
        return $this->gravatar_max_rat;
    }

    /**
     * Gets the value of admin_g_id.
     *
     * @return mixed
     */
    public function getAdminGId()
    {
        return $this->admin_g_id;
    }

    /**
     * Sets the value of admin_g_id.
     *
     * @param mixed $admin_g_id the admin g id
     *
     * @return self
     */
    private function setAdminGId($admin_g_id)
    {
        $this->admin_g_id = $admin_g_id;

        return $this;
    }

    /**
     * Gets the value of enable_admin.
     *
     * @return mixed
     */
    public function getEnableAdmin()
    {
        return $this->enable_admin;
    }

    /**
     * Sets the value of enable_admin.
     *
     * @param mixed $enable_admin the enable admin
     *
     * @return self
     */
    private function setEnableAdmin($enable_admin)
    {
        $this->enable_admin = $enable_admin;

        return $this;
    }

    /**
     * Gets the value of is_admin.
     *
     * @return mixed
     */
    public function getIsAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Sets the value of is_admin.
     *
     * @param mixed $is_admin the is admin
     *
     * @return self
     */
    public function setIsAdmin($is_admin)
    {
        $this->is_admin = $is_admin;

        return $this;
    }

    /**
     * Gets the value of is_connect.
     *
     * @return mixed
     */
    public function getIsConnect()
    {
        return $this->is_connect;
    }

    /**
     * Sets the value of is_connect.
     *
     * @param mixed $is_connect the is connect
     *
     * @return self
     */
    public function setIsConnect($is_connect)
    {
        $this->is_connect = $is_connect;

        return $this;
    }

    /**
     * Gets the value of default_g_id.
     *
     * @return mixed
     */
    public function getDefaultGId()
    {
        return $this->default_g_id;
    }

    /**
     * Sets the value of default_g_id.
     *
     * @param mixed $default_g_id the default g id
     *
     * @return self
     */
    private function setDefaultGId($default_g_id)
    {
        $this->default_g_id = $default_g_id;

        return $this;
    }

    /**
     * Gets the value of col_groups.
     *
     * @return mixed
     */
    public function getColGroups()
    {
        return $this->col_groups;
    }

    /**
     * Sets the value of col_groups.
     *
     * @param mixed $col_groups the col groups
     *
     * @return self
     */
    private function setColGroups($col_groups)
    {
        $this->col_groups = $col_groups;

        return $this;
    }

    /**
     * Gets the value of group_id_admin.
     *
     * @return mixed
     */
    public function getGroupIdAdmin()
    {
        return $this->group_id_admin;
    }

    /**
     * Sets the value of group_id_admin.
     *
     * @param mixed $group_id_admin the group id admin
     *
     * @return self
     */
    private function setGroupIdAdmin($group_id_admin)
    {
        $this->group_id_admin = $group_id_admin;

        return $this;
    }

    /**
     * Gets the value of connectionOptions.
     *
     * @return mixed
     */
    public function getConnectionOptions()
    {
        return $this->connectionOptions;
    }

    /**
     * Sets the value of connectionOptions.
     *
     * @param mixed $connectionOptions the connection options
     *
     * @return self
     */
    private function setConnectionOptions($connectionOptions)
    {
        $this->connectionOptions = $connectionOptions;

        return $this;
    }
}
?>