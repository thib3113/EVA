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
                                    'uid'            => 'int',
                                    'username'       => 'string',
                                    'pass'           => 'string',
                                    'group_id'       => 'int',
                                    'email'          => 'longstring',
                                    'create_time'    => 'timestamp',
                                    'plugins_list'   => 'TEXT',
                                    'dashboard_list' => 'TEXT',
                                    'avatar'         =>  'longstring',
                                    'token'         =>  'TEXT'
                                    );

    //gravatar
    private $gravatar_size = 80; // 1 - 2048 definie la taille de l'image
    private $gravatar_default = 404; // image par défault de l'image [ 404 | mm | identicon | monsterid | wavatar ]
    private $gravatar_max_rat = 'g'; // maximum rating ( aucune idée de à quoi ça sert )

    
    private $enable_admin = true; // active les admins
    public $is_admin = false; // l'utilisateur est il un administrateur
    public $is_connect = false; // l'utilisateur est il connecté

    private $session_name = '';
    private $cookie_name = '';
    private $cookie_time = 63072000;//2 ans
    private $default_g_id = 1;
    private $col_groups = "group_id"; //nom de la colonne du groupe
    private $group_id_admin = 0; //valeur du groupe si l'utilisateur es admin

    function __construct(){
        $this->session_name = PROGRAM_NAME.'_auth';
        $this->cookie_name = PROGRAM_NAME.'_auth';

        parent::__construct();
    }

    public function connect($user, $password, $rememberMe = false, $needEncrypt = true){
        $password = ($needEncrypt)? $this->preparePasswd($user, $password) : $password; //on prépare le mot de passe si celui ci n'as pas était hashé auparavant 

        //on fait une requete du password avec le mot de passe
        $result = SgdbManager::sgbdSelect(array('*'), array("username" => $user,"pass" => $password), null,null,null,  __FILE__, __LINE__ );
        $result = $result->fetch();
        
        if(empty($result)){// si cela ne retourne rien, c'est que le mot de passe ne correspond pas à cet identifiant 
            return false;
        }
        else{
            $this->createSession($rememberMe);
            $this->fillObject($result['id']);
            return true;
        }
    }

    public function createSession($cookie){
        $_SESSION[$this->session_name] = serialize(array($this->username, $this->uid, $this->token));
        if($cookie)// si on demande de se souvenir, on crée un cookie
            setcookie($this->cookie_name, serialize( array($this->username, $this->uid, $this->token) ), time()+$this->cookie_time, '/' );
    }

    public function setToken(){
        $this->token = Functions::randomStr(rand(15, 127));
    }

    public function setUid(){
        $uid = rand(0,9);
        for ($i=0; $i <= 99 ; $i++) { 
            $uid .= rand(0,9);
        }
        $this->uid = $uid;
    }

    private function fillObject($id){
        if(!is_numeric($id))
            return false;

            $result = self::sgbdSelect( array_keys($this->object_fields) , array("id" => $id), null, null, null, __FILE__, __LINE__);
            $result = $result->fetch();
            $i = 0;
            foreach($this->object_fields as $field=>$type){
                    $this->$field = $result[$field];
                    $i++;
            }
    }

    public function isConnect(){

        $GLOBALS['is_connect'] = false; // on initialise les globales
        $GLOBALS['is_admin'] = false; // on initialise les globales

        if(!empty($_COOKIE[$this->cookie_name])){ //si le cookie existe
            //on crée la session correspondante ( qui fonctionnera, ou pas )
            $_SESSION[$this->session_name] = $_COOKIE[$this->cookie_name];
        }

        if(isset($_SESSION[$this->session_name])){ //on regarde si la session existe
            $session_infos = $_SESSION[$this->session_name]; //on renomme la session
            $session_infos = unserialize($session_infos); //on explose la session ( celle ci est de la forme username-passwordhashé )

            //on met les bons résultats dans les bonnes variables
            $username = $session_infos[0]; 
            $uid = $session_infos[1];
            $token = $session_infos[2];
        
            $result = SgdbManager::sgbdSelect(array('*'), array("username" => $username,"uid" => $uid), null,null,null,  __FILE__, __LINE__ );
            $result = $result->fetch();



            if(empty($result))
                return false;

            //on vérifie que le token soit le bon
            if($token != $result["token"]){
                /*si il n'est pas bon, cela peut être une attaque par vol de cookie
                on génère un nouveau token, ce qui au pire déconnectera le vrai utilisateur */
                $this->setToken();
                $this->save("token");

            }


            var_dump($result);
        }  
    }

    public function getUserInfos(){
        $id = $this->id;
        //création de la query
        $result_query = SgdbManager::sgbdSelect(array('*'), array("id" => $id), null,null,null,  __FILE__, __LINE__ );

        $return = $result_query->fetch();
        $return['avatar'] = empty($return['avatar'])? $this->getGravatar($return['email']) : $config['base_url'].'/vues/img_up/profils/'.$return['avatar'];
        return $return; 
        
    }

    public function preparePasswd($username, $pass){ //on prépare le mot de passe à un stockage en bdd

        $hashKey = hash("adler32", $username);
        $pass_temp = $username.$hashKey.$pass;
        if(DB_HASH)
            $pass_temp = hash(DB_HASH, $pass_temp);

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
     * @param mixed $id, the id, 
     *
     * @return self
     */
    public function setId($id){
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
        $currentDashboardList[] = $dashboard;
        $this->setDashboardList($currentDashboardList); 
    }

    public function getPluginsList(){
        return unserialize($this->plugins_list);
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