<?php
Class Connection extends SgdbManager{
    protected $id;
    protected $uid;
    protected $token;
    protected $browser_infos;
    protected $current_ip;
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

    private $session_name = '';
    private $session_connect = '';
    private $cookie_name = '';
    private $cookie_time = 63072000;//2 ans

    private $username;

    public function __construct(){
        $this->session_name = PROGRAM_NAME.'_auth';
        $this->cookie_name = PROGRAM_NAME.'_auth';
        $this->session_connect = PROGRAM_NAME.'_connect';


    }

    public function open($user = null, $password = null, $rememberMe = null, $needEncrypt = null){

        if(empty($user) || empty($password) || empty($rememberMe) || empty($needEncrypt)){
            return $this->alreadyOpen();
        }
        else{
            return $this->connect($user, $password, $rememberMe, $needEncrypt);
        }
    }

    public function connect($user, $password, $rememberMe = false, $needEncrypt = true){
        $password = ($needEncrypt)? $this->preparePasswd($user, $password) : $password; //on prépare le mot de passe si celui ci n'as pas était hashé auparavant

        //on fait une requete du password avec le mot de passe
        $result = SgdbManager::sgbdSelect(array('*'), array("username" => $user,"pass" => $password), "users",null,null, null,  __FILE__, __LINE__ );
        $result = $result->fetch();

        $this->username = $result['username'];
        $this->set_uid_infos();

        if(empty($result)){// si cela ne retourne rien, c'est que le mot de passe ne correspond pas à cet identifiant
            return false;
        }
        else{
            $this->id = $result['id'];
            $this->set_uid_infos($result['id']);

            $this->createSession($rememberMe);
            return $result["id"];
        }
    }

    public function close(){
        //on supprime la session
        $_SESSION[$this->session_name] = NULL;
        $_SESSION[$this->session_connect] = NULL;

        //on vide le cookie en le faisant expirer il y à une heure
        setcookie($this->cookie_name, "", time()-3600, '/' );
        $_COOKIE[$this->cookie_name] = NULL;
    }

    public function alreadyOpen(){
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
            $this->uid = $session_infos[1];
            $this->token = $session_infos[2];

            $result = SgdbManager::sgbdSelect(array('*'), array("username" => $this->username), "users", null, null, null,  __FILE__, __LINE__ );
            $result = $result->fetch();



            if(empty($result))
                return false;


            // $this->id = $result['id'];
            $this->set_uid_infos($this->uid);

            //permet de sauter la vérification du token . Cela permet d'avoir 2 requètes ajax asynchrone
            if(isset($_SESSION[$this->session_connect]) && $_SESSION[$this->session_connect] == empty($_COOKIE['PHPSESSID'])? $_COOKIE['PHPSESSID'] : SID){

                //si le token est bon on connecte
                $this->createSession();
                return $result['id'];
            }

            //on vérifie que le token soit le bon
            if($this->token != $this->token){
                //le token n'es pas bon, on le change donc pour éviter le bruteforce
                $this->setToken();

                //on efface le cookie et la session
                $this->close();

                return false;
            }
            else{
                //si le token est bon on connecte
                $this->createSession();
                return $result['id'];
            }
        }
        return false;

    }

    public function preparePasswd($username, $pass){ //on prépare le mot de passe à un stockage en bdd

        $hashKey = hash("adler32", $username);
        $pass_temp = $username.$hashKey.$pass;
        if(DB_HASH)
            $pass_temp = hash(DB_HASH, $pass_temp);
        // var_dump($pass_temp);
        return $pass_temp;
    }

    public function setToken(){
        $this->token = Functions::randomStr(rand(100, 127));
        $this->sgbdSave();
        // $this->_query("UPDATE ".DB_PREFIX.$this->uid_table_name." SET token=?, time=?  WHERE uid=?", array($this->token, time(), $this->current_uid), __FILE__, __LINE__);

        return true;
    }

    public function saveIp(){
        if(!in_array($_SERVER['REMOTE_ADDR'], $this->current_ip_list)){
            $this->current_ip_list[] = $_SERVER['REMOTE_ADDR'];
            $this->sgbdSave();
            // $this->_query("UPDATE ".DB_PREFIX.$this->uid_table_name." SET ips=? WHERE uid=?", array(serialize($this->current_ip_list), $this->current_uid), __FILE__, __LINE__);
        }

    }

    public function createSession($cookie = false){

        //on génère un nouveau token
        $this->setToken();

        // var_dump($_COOKIE);
        // var_dump("on crée la session");
        //on crée la session
        $_SESSION[$this->session_name] = serialize(array($this->username, $this->uid, $this->token));
        $_SESSION[$this->session_connect] = !empty($_COOKIE['PHPSESSID'])? $_COOKIE['PHPSESSID'] : SID;
        // var_dump($_SESSION);
        // var_dump($this->username);
        // var_dump($this->uid);
        // var_dump($this->token);
        // var_dump(unserialize($_SESSION['EVA_auth']));

        //et un cookie au besoin
        if($cookie || !empty($_COOKIE[$this->cookie_name])){
            // var_dump("on crée le cookie");
            setcookie($this->cookie_name, $_SESSION[$this->session_name], time()+$this->cookie_time, '/' );
        }

        $this->saveIp();
    }

    public function generateUid(){
        $uid = rand(0,9);

        for ($i=0; $i <= 20 ; $i++) {
            $uid .= rand(0,9);
        }

        $this->uid = $uid;
    }

    public function set_uid_infos(){
        if(!empty($this->uid)){
            $result = SgdbManager::sgbdSelect(array('*'), array("uid" => $this->uid), null, null, null, null,  __FILE__, __LINE__ );
            $result = $result->fetch();
        }
        else{
            $result = SgdbManager::sgbdSelect(array('*'), array("browser_infos" => $_SERVER['HTTP_USER_AGENT']), null, null, null, null,  __FILE__, __LINE__ );
            $result = $result->fetch();
            if(!$result){
                $this->generateUid();
                $result['uid'] = $this->uid;
                $result['token'] = "";
                $result['ips'] = serialize(array($_SERVER['REMOTE_ADDR']));
                $this->sgbdSave();
                // $this->_query("INSERT INTO ".DB_PREFIX.$this->uid_table_name." (uid, browser_infos, ips, id, time) VALUES (?, ?, ?, ?, ?)", array($this->current_uid, $_SERVER['HTTP_USER_AGENT'], $result['ips'], $this->id, time() ), __FILE__, __LINE__);
            }
        }
        $this->current_ip = $_SERVER['REMOTE_ADDR'];
        $this->current_ip_list = Functions::secureUnserialize($result['ips']);
        $this->current_user_agent = $_SERVER['HTTP_USER_AGENT'];
        $this->current_uid = $result['uid'];
        $this->token = $result['token'];
        return true;
    }
}