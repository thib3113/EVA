<?php

class UsersManager extends SgdbManager{
    protected $id, $name,$pass, $group_id, $email, $create_time, $plugins_list, $dashboard_list, $avatar;
    protected $TABLE_NAME = "uid";
    protected $object_fields= array(
                                    'uid'          => 'string',
                                    'token'        =>  'TEXT',
                                    'user_id'      =>  'int',
                                    'browser_info' =>  'string',
                                    'ips'          =>  'string',
                                    );

    //les variables suivantes seront précédées du nom du programme
    protected $session_name = "_auth";
    protected $cookie_name = "_auth";
    protected $session_connect = "_connect";

    function __construct(){
        $this->session_name = PROGRAM_NAME.$this->session_name;
        $this->cookie_name = PROGRAM_NAME.$this->cookie_name;
        $this->session_connect = PROGRAM_NAME.$this->session_connect;

        parent::__construct();
    }

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

    public function connect($user, $password, $rememberMe = false, $needEncrypt = true){
        $password = ($needEncrypt)? $this->preparePasswd($user, $password) : $password; //on prépare le mot de passe si celui ci n'as pas était hashé auparavant

        //on fait une requete du password avec le mot de passe
        $result = SgdbManager::sgbdSelect(array('*'), array("username" => $user,"pass" => $password), User::getTableName(), null, null, null,  __FILE__, __LINE__ );
        $result = $result->fetch();

        $result2 = SgdbManager::sgbdSelect(array('*'), array("user_id" => $result['id']), null, null, null, null,  __FILE__, __LINE__ );
        $result2 = $result2->fetch();

        if(empty($result)){// si cela ne retourne rien, c'est que le mot de passe ne correspond pas à cet identifiant
            return false;
        }
        else{
            $result['current_uid'] = $result2['uid'];
            $newUser = new User($result);
            $newUser->createSession($rememberMe);
            $this->token = $newUser->current_token;
            return $newUser;
        }
    }

        public function isConnect(){

        $GLOBALS['is_connect'] = false; // on initialise les globales
        $GLOBALS['is_admin'] = false; // on initialise les globales

        if(!empty($_COOKIE[$this->cookie_name])){ //si le cookie existe
            //on crée la session correspondante ( qui fonctionnera, ou pas )
            $_SESSION[$this->session_name] = $_COOKIE[$this->cookie_name];
        }

        // Bien, mais en ajax, mais pose des probleme avec les requete ajax asynchrone
        if(isset($_SESSION[$this->session_name])){ //on regarde si la session existe
            $session_infos = $_SESSION[$this->session_name]; //on renomme la session
            $session_infos = unserialize($session_infos);

            //on met les bons résultats dans les bonnes variables
            $this->user_id = $session_infos[0];
            $this->uid = $session_infos[1];
            $this->token = $session_infos[2];

            $result = SgdbManager::sgbdSelect(array('*'), array("user_id" => $this->user_id,"uid" => $this->uid), null,null,null,null,  __FILE__, __LINE__ );
            $result = $result->fetch();

            if(empty($result))
                return false;

            //on ajoute pour l'user
            $result['current_uid'] = $this->uid;
            $result['current_token'] = $this->token;

            //permet de sauter la vérification du token . Cela permet d'avoir 2 requètes ajax asynchrone
            if(isset($_SESSION[$this->session_connect]) && $_SESSION[$this->session_connect] == empty($_COOKIE['PHPSESSID'])? $_COOKIE['PHPSESSID'] : SID){
                $newUser = new User($result);
                $newUser->createSession($rememberMe);
                return $newUser;
            }

            //on vérifie que le token soit le bon
            if($this->token != $result["token"]){
                //le token n'es pas bon, on le change donc pour éviter le bruteforce
                $this->setToken();
                $this->save("token");

                //on efface le cookie et la session
                $this->disconnect();

                return false;
            }
            else{
                $newUser = new User($result);
                $newUser->createSession($rememberMe);
                return $newUser;
            }
        }

    }

}