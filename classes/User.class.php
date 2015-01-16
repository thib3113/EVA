<?php


Class User extends SgdbManager{
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

    //gravatar
    private $gravatar_size = 80; // 1 - 2048 definie la taille de l'image
    private $gravatar_default = 404; // image par défault de l'image [ 404 | mm | identicon | monsterid | wavatar ]
    private $gravatar_max_rat = 'g'; // maximum rating ( aucune idée de à quoi ça sert )


    private $uid_table_name = "uid";
    private $current_uid = 0; //uid courant
    private $admin_g_id = 1;
    private $enable_admin = true; // active les admins
    public $is_admin = false; // l'utilisateur est il un administrateur
    public $is_connect = false; // l'utilisateur est il connecté

    private $session_name = '';
    private $session_connect = '';
    private $cookie_name = '';
    private $cookie_time = 63072000;//2 ans
    private $default_g_id = 2;
    private $col_groups = "group_id"; //nom de la colonne du groupe
    private $group_id_admin = 0; //valeur du groupe si l'utilisateur es admin

    function __construct($user = null, $password = null, $rememberMe = false, $needEncrypt = true){
        $this->session_name = PROGRAM_NAME.'_auth';
        $this->cookie_name = PROGRAM_NAME.'_auth';
        $this->session_connect = PROGRAM_NAME.'_connect';

        if(empty($user) || empty($password) || empty($rememberMe) || empty($needEncrypt)){
            $this->isConnect();
        }
        else{
            $this->connect($user, $password, $rememberMe, $needEncrypt);
        }
        parent::__construct();
    }

    public function connect($user, $password, $rememberMe = false, $needEncrypt = true){
        $password = ($needEncrypt)? $this->preparePasswd($user, $password) : $password; //on prépare le mot de passe si celui ci n'as pas était hashé auparavant

        //on fait une requete du password avec le mot de passe
        $result = SgdbManager::sgbdSelect(array('*'), array("username" => $user,"pass" => $password), null,null,null, null,  __FILE__, __LINE__ );
        $result = $result->fetch();


        if(empty($result)){// si cela ne retourne rien, c'est que le mot de passe ne correspond pas à cet identifiant
            return false;
        }
        else{
            $this->id = $result['id'];
            $this->set_uid_infos();

            $this->fillObject($result['id']);
            $this->createSession($rememberMe);
            return $this;
        }
    }


    public function is_admin(){
        if($this->group_id == $this->admin_g_id){
            $this->is_admin = true;
            $GLOBALS['is_admin'] = true;
            return true;
        }
    }

    public function createSession($cookie = false){

        //on génère un nouveau token
        $this->setToken();
        $this->_query("UPDATE ".DB_PREFIX.$this->uid_table_name." SET token=? WHERE uid=?", array($this->current_token, $this->current_uid), __FILE__, __LINE__);

        //on génère les variables d'informations
        $GLOBALS['is_connect'] = true;
        $this->is_connect = true;
        $this->is_admin();


        //on crée la session
        $_SESSION[$this->session_name] = serialize(array($this->username, $this->current_uid, $this->current_token));
        $_SESSION[$this->session_connect] = !empty($_COOKIE['PHPSESSID'])? $_COOKIE['PHPSESSID'] : SID;

        //et un cookie au besoin
        if($cookie || !empty($_COOKIE[$this->cookie_name])){
            setcookie($this->cookie_name, $_SESSION[$this->session_name], time()+$this->cookie_time, '/' );
        }

        $this->saveIp();

    }

    public function setToken(){
        $this->current_token = Functions::randomStr(rand(100, 127));
        $this->_query("UPDATE ".DB_PREFIX.$this->uid_table_name." SET token=?, time=?  WHERE uid=?", array($this->current_token, time(), $this->current_uid), __FILE__, __LINE__);

        return true;
    }
    public function generateUid(){
        $uid = rand(0,9);

        for ($i=0; $i <= 20 ; $i++) {
            $uid .= rand(0,9);
        }

        $this->current_uid = $uid;
    }

    public function saveIp(){
        if(!in_array($_SERVER['REMOTE_ADDR'], $this->current_ip_list)){
            $this->current_ip_list[] = $_SERVER['REMOTE_ADDR'];
            $this->_query("UPDATE ".DB_PREFIX.$this->uid_table_name." SET ips=? WHERE uid=?", array(serialize($this->current_ip_list), $this->current_uid), __FILE__, __LINE__);
        }

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
        $this->current_ip_list = unserialize($result['ips']);
        $this->current_user_agent = $_SERVER['HTTP_USER_AGENT'];
        $this->current_uid = $result['uid'];
        $this->current_token = $result['token'];
        return true;
    }

    private function fillObject($id){
        if(!is_numeric($id))
            return false;

            $result = self::sgbdSelect( array_keys($this->object_fields) , array("id" => $id), null, null, null, null, __FILE__, __LINE__);
            $result = $result->fetch();
            $i = 0;
            foreach($this->object_fields as $field=>$type){
                    $this->$field = $result[$field];
                    $i++;
            }
    }

    public function disconnect(){
        $_SESSION[$this->session_name] = NULL;
        $_SESSION[$this->session_connect] = NULL;

        setcookie($this->cookie_name, "", time()-3600, '/' );
        $_COOKIE[$this->cookie_name] = NULL;
    }

    public function isConnect(){
        if(!empty($_COOKIE[$this->cookie_name])){ //si le cookie existe
            //on crée la session correspondante ( qui fonctionnera, ou pas )
            $_SESSION[$this->session_name] = $_COOKIE[$this->cookie_name];
        }

        // Bien, mais en ajax, mais pose des probleme avec les requete ajax asynchrone
        if(isset($_SESSION[$this->session_name])){ //on regarde si la session existe
            $session_infos = $_SESSION[$this->session_name]; //on renomme la session
            $session_infos = unserialize($session_infos);

            //on met les bons résultats dans les bonnes variables
            $this->username = $session_infos[0];
            $this->current_uid = $session_infos[1];
            $this->current_token = $session_infos[2];

            $result = SgdbManager::sgbdSelect(array('*'), array("username" => $this->username), null, null, null, null,  __FILE__, __LINE__ );
            $result = $result->fetch();



            if(empty($result))
                return false;


            $this->id = $result['id'];
            $this->set_uid_infos();

            //permet de sauter la vérification du token . Cela permet d'avoir 2 requètes ajax asynchrone
            if(isset($_SESSION[$this->session_connect]) && $_SESSION[$this->session_connect] == empty($_COOKIE['PHPSESSID'])? $_COOKIE['PHPSESSID'] : SID){

                $this->fillObject($result['id']);
                //si le token est bon on connecte
                $this->createSession();
                return $this;
            }

            //on vérifie que le token soit le bon
            if($this->current_token != $this->current_token){
                //le token n'es pas bon, on le change donc pour éviter le bruteforce
                $this->setToken();

                //on efface le cookie et la session
                $this->disconnect();

                return false;
            }
            else{
                $this->fillObject($result['id']);
                //si le token est bon on connecte
                $this->createSession();
                return $this;
            }
        }

    }

    public function getUserInfos(){
        $id = $this->id;
        //création de la query
        $result_query = SgdbManager::sgbdSelect(array('*'), array("id" => $id), null, null,null,null,  __FILE__, __LINE__ );

        $return = $result_query->fetch();
        $return['avatar'] = empty($return['avatar'])? $this->getGravatar($return['email']) : $config['base_url'].'/vues/img_up/profils/'.$return['avatar'];
        return $return;

    }

    public function preparePasswd($username, $pass){ //on prépare le mot de passe à un stockage en bdd

        $hashKey = hash("adler32", $username);
        $pass_temp = $username.$hashKey.$pass;
        if(DB_HASH)
            $pass_temp = hash(DB_HASH, $pass_temp);
        // var_dump($pass_temp);
        return $pass_temp;
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
        $this->username = $username;
        return true;
    }
    public function setPass($pass, $name = false, $need_encode = false){
        if($need_encode && !$name)return false;
        $this->pass = $need_encode? self::preparePasswd($name, $pass) : $name;
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
    }

    public function setDashboardList(array $dashboard_list){
        $this->dashboard_list = serialize($dashboard_list);
    }

    public function addDashboard(array $dashboard){
        $currentDashboardList = unserialize($this->dashboard_list);

        if(!is_array($dashboard))
            return false;

        if(empty($dashboard["position"]))
            $dashboard["position"] = count($this->dashboard_list);

        $currentDashboardList[] = $dashboard;
        $this->setDashboardList($currentDashboardList);
    }

    public function getPluginsList(){
        return Functions::secureUnserialize($this->plugins_list);
    }

    public function getDashboardList(){
        $dashboard_list = array();
        $dashboard_list_temp = unserialize($this->dashboard_list);
        if($dashboard_list_temp){
            uasort($dashboard_list_temp, function($a,$b){
                                                if(!empty($a['position']) && !empty($b['position']) )
                                                    return $a['position']>$b['position']?1:-1;
                                                else
                                                    return 0; 
                                                });
            foreach ($dashboard_list_temp as $key => $value) {
                $dashboard_list[$key] = $value[0];
            }
        }
        return $dashboard_list;
    }

    public function getAvatar(){
        return !empty($this->avatar)? $this->avatar : $this->getGravatar();
    }

    public function getName(){
        return !empty($this->name) && !empty($this->forname)? $this->username." ".$this->forname : $this->username;
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
}
?>