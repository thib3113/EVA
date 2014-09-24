<?php


Class User {
    protected $id, $name,$pass, $group_id, $email, $create_time;
    protected $TABLE_NAME = "Users";
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

    private $session_name = '';
    private $cookie_name = '';
    private $cookie_time = 131400000;//100 ans
    private $hash = "md5";

    function __construct($user = null, $password = null, $remember_me = null){
        $this->session_name = PROGRAM_NAME.'_auth';
        $this->cookie_name = PROGRAM_NAME.'_auth';

        if(!empty($user) && !empty($password))
            $this->connect($user, $password, $remember_me);

        parent::__construct();
        

    }

    public function connect($user, $password, $remember_me = false, $need_encrypt = true){
        $password = ($need_encrypt)? $this->preparePasswd($user, $password) : $password; //on prépare le mot de passe si celui ci n'as pas était hashé auparavant 

        //on fait une requete du password avec le mot de passe
        $result_query = SgdbManager::sgbdSelect(DB_PREFIX.$this->TABLE_NAME, array('*'), array("user" => $user,"pass" => $password), null,null,null,  __FILE__, __LINE__ );
        $result = $result_query->fetch();

        // echo $this->preparePasswd($user, $password);
        
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

    public function getUserInfos(){
        $id = $this->id;
        //création de la query
        $query = 'SELECT * FROM '.$this->table_users.' WHERE id='.$id;
        $result = $this->_query($query);
        $return = $result->fetch();
        $return['avatar'] = empty($return['avatar'])? $this->getGravatar($return['email']) : $config['base_url'].'/vues/img_up/profils/'.$return['avatar'];
        return $return; 
        
    }

    public function preparePasswd($username, $pass){ //on prépare le mot de passe à un stockage en bdd

        $pass_temp = $username.$pass;
        if($this->hash)
            $pass_temp = hash($this->hash, $pass_temp);

        return $pass_temp;
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

        return $this;
    }
}
?>