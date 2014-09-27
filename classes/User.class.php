<?php


Class User extends SgdbManager{
    protected $id, $name,$pass, $group_id, $email, $create_time;
    protected $TABLE_NAME = "users";
    protected $object_fields= array(
                                    'id'          => 'key',
                                    'name'        => 'string',
                                    'pass'        => 'string',
                                    'group_id'    => 'int',
                                    'email'       => 'longstring',
                                    'create_time' => 'timestamp',
                                    'email'       => 'longstring',
                                    'avatar'      =>  'longstring'
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
    private $cookie_time = 131400000;//100 ans
    private $hash = "md5";
    private $default_g_id = 1;
    private $col_groups = "group_id"; //nom de la colonne du groupe
    private $group_id_admin = 0; //valeur du groupe si l'utilisateur es admin

    function __construct(){
        $this->session_name = PROGRAM_NAME.'_auth';
        $this->cookie_name = PROGRAM_NAME.'_auth';

        parent::__construct();
    }

    public function connect($user, $password, $remember_me = false, $need_encrypt = true){
        $password = ($need_encrypt)? $this->preparePasswd($user, $password) : $password; //on prépare le mot de passe si celui ci n'as pas était hashé auparavant 

        //on fait une requete du password avec le mot de passe
        $result_query = SgdbManager::sgbdSelect(DB_PREFIX.$this->TABLE_NAME, array('*'), array("name" => $user,"pass" => $password), null,null,null,  __FILE__, __LINE__ );

        if(!$result_query)
            return false;

        $result = $result_query->fetch();
        
        if(empty($result)){// si cela ne retourne rien, c'est que le mot de passe ne correspond pas à cet identifiant 
            return false;
        } 
        else{
            //on crée une session à partir du mot de passe hashé et du nom de compte
            $_SESSION[$this->session_name] = serialize(array($user, $password));
            if($remember_me)// si on demande de se souvenir, on crée un cookie
                setcookie($this->cookie_name, serialize( array($user, $password) ), time()+$this->cookie_time, '/' );
            $this->id = $result['id'];
            return $this->getUserInfos();
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
            $password = $session_infos[1];

            $result_connect = $this->connect($username, $password, false, false); //on tente de connecter l'utilisateur
            $GLOBALS['is_connect'] = $this->is_connect; // on initialise les globales
            $GLOBALS['is_admin'] = $this->is_admin; // on initialise les globales
            if($result_connect){ //si le résultat de la connexion n'est pas faux
                $user = $this->getUserInfos($result_connect); //on retourne les informations de l'utilisateur
                if($this->enable_admin && $user[$this->col_groups] == $this->group_id_admin) //si les admins sont activés, et que l'utilisateur est un admin
                    $GLOBALS['is_admin'] = true; //on crée une variable $is_admin qui sortira
                else
                    $GLOBALS['is_admin'] = false;
                $GLOBALS['is_connect'] = true;//on crée la variable $is_connect
                return $user;
            }
            else
                return false;
        }
        else
            return false; //si la session n'existe pas, on retourne faux
    }

    public function getUserInfos(){
        $id = $this->id;
        //création de la query
        $result_query = SgdbManager::sgbdSelect(DB_PREFIX.$this->TABLE_NAME, array('*'), array("id" => $id), null,null,null,  __FILE__, __LINE__ );

        $return = $result_query->fetch();
        $return['avatar'] = empty($return['avatar'])? $this->getGravatar($return['email']) : $config['base_url'].'/vues/img_up/profils/'.$return['avatar'];
        return $return; 
        
    }

    public function preparePasswd($username, $pass){ //on prépare le mot de passe à un stockage en bdd

        $pass_temp = $username.$pass;
        if($this->hash)
            $pass_temp = hash($this->hash, $pass_temp);

        return $pass_temp;
    }

    public function createUser($name, $pass, $email, $g_id = null){
        if(empty($g_id) )
            $g_id = $this->default_g_id;

        if(!$this->setName($name))
            return false;
        if(!$this->setPass($pass, $name, true))
            return false;
        if(!$this->setEmail($email))
            return false;
        if(!$this->setGroupId($g_id))
            return false;
        if(!$this->setCreateTime(time()))
            return false;
        if(!$this->setAvatar(""))
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
    public function setName($name){
        $this->name = $name;
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