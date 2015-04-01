<?php
Class Connection extends SgdbManager{
    protected $id;
    protected $uid;
    protected $token;
    protected $userAgent;
    protected $application = 'web';
    protected $app_can_speak;
    protected $current_ip;
    protected $ips;
    protected $time;
    protected $userId;
    protected $expiration;
    protected $TABLE_NAME = "connect";
    protected $object_fields= array(
                               'id'            => 'key',
                               'uid'           => 'TEXT',
                               'token'         => 'TEXT',
                               'userAgent'     => 'TEXT',
                               'application'   => 'TEXT',
                               'app_can_speak' => 'boolean',
                               'ips'           => 'TEXT',
                               'time'          => 'int',
                               'userId'        => 'int',
                               'expiration'    => 'int',
                               );

    private $session_name = '';
    private $session_connect = '';
    private $cookie_name = '';
    private $cookie_time = 63072000;//2 ans

    private $username;

    private $debug;
    public function __construct(){
        
        $this->session_name = PROGRAM_NAME.'_auth';
        $this->cookie_name = PROGRAM_NAME.'_auth';
        $this->session_connect = PROGRAM_NAME.'_connect';

        $this->expiration = time();
        
        //on prend les configurations
        $args = debug_backtrace()[0]["args"];
        foreach ($args as $arg) {
            if(!is_null($arg)){
                foreach ($arg as $key => $value) {
                    if(method_exists($this,"set".ucfirst($key)))
                        $this->{"set".ucfirst($key)}($value);
                }
            }
        }
    }

    public function open($user = null, $password = null, $rememberMe = false, $needEncrypt = true){

        
        if(empty($user) || empty($password)){
            return $this->alreadyOpen();
        }
        else{
            return $this->connect($user, $password, $rememberMe, $needEncrypt);        
        }
    }

    public function connect($user, $password, $rememberMe = false, $needEncrypt = true){

        
        $password = ($needEncrypt)? Functions::preparePasswd($user, $password) : $password; //on prépare le mot de passe si celui ci n'as pas était hashé auparavant

        $result = SgdbManager::sgbdSelect(array('*'), array("username" => $user, "pass" => $password), "users", null, null, null,  __FILE__, __LINE__ );
        $result = $result->fetch();

        //si l'utilisateur n'existe pas ou sont mot de passe est incorrect, on sort
        if( $result === false )
            return false;


        //////////////////////////////////////////////////
        // À partir d'ici l'utilisateur est authentifié //
        //////////////////////////////////////////////////
        //on stocke certaines valeur de l'utilisateur
        $this->setUsername($result["username"]);
        $this->setUserId($result["id"]);

        //on cherche une uid déjà existante pour cet utilisateur
        $result = SgdbManager::sgbdSelect(array('*'), array("userId" => $result['id'], "userAgent" => $this->userAgent ), null, null, null, null,  __FILE__, __LINE__ );
        $result = $result->fetch();

        if($result){
            $this->set_infos();
        }
        else{
            $this->set_infos($result["uid"]);
        }

        $this->createSession($rememberMe);
        $this->sgdbSave();

        return $result["userId"];
    }

    public function close(){

        
        //on supprime la session
        $_SESSION[$this->session_name] = NULL;
        $_SESSION[$this->session_connect] = NULL;

        //on vide le cookie en le faisant expirer il y à une heure
        setcookie($this->cookie_name, "", time()-3600, '/' );
        $_COOKIE[$this->cookie_name] = NULL;
    }

    public function can_speak(){
        return $this->app_can_speak;
    }

    public function alreadyOpen(){

        do{
            if(isset($_SESSION[$this->session_name])){ //on regarde si la session existe

                $session_infos = $_SESSION[$this->session_name]; //on renomme la session
                $session_infos = unserialize($session_infos);

                //on met les bons résultats dans les bonnes variables
                $username = $session_infos[0];
                $uid = $session_infos[1];
                $token = $session_infos[2];

                $result = SgdbManager::sgbdSelect(array('*'), array("uid" => $uid), null, null, null, null,  __FILE__, __LINE__ );
                $connection = $result->fetch();


                

                if(empty($connection)){
                    $this->close();
                    return false;
                }

                $result = SgdbManager::sgbdSelect(array('*'), array("username" => $username, "id" => $connection["userId"]), "users", null, null, null,  __FILE__, __LINE__ );
                $user = $result->fetch();
                

                if(empty($user)){
                    $this->close();
                    return false;
                }

                //on vérifie que l'expiration ne soit pas dépassé
                if($connection["expiration"] > time() ){ 

                    
                    //on vérifie que le token soit le bon
                    if($token != $connection["token"]){

                        
                        //le token n'es pas bon, on le change donc pour éviter le bruteforce
                        $this->setToken();
                        $this->sgdbSave();
                        //on efface le cookie et la session
                        $this->close();

                        return false;
                    }
                    else{
                        
                        $this->setUsername($user["username"]);                     
                        $this->set_infos($connection['uid']);
                        //si le token est bon on connecte
                        $this->createSession(empty($_COOKIE[$this->cookie_name]));

                        $this->sgdbSave();
                        return $user['id'];
                    }
                }
                else{

                    
                    $this->setUsername($user["username"]); 
                    $this->set_infos($connection['uid']);
                    //si le token est bon on connecte
                    $this->createSession(empty($_COOKIE[$this->cookie_name]));

                    return $user['id'];
                }
            }

            //si la connection n'as pas fonctionné avec la session, on teste avec le cookie
            $connection_via_cookie = false;
            if(!empty($_COOKIE[$this->cookie_name])){ //si le cookie existe
                //on crée la session correspondante ( qui fonctionnera, ou pas )
                $_SESSION[$this->session_name] = $_COOKIE[$this->cookie_name];
                $connection_via_cookie = true;
            }
        }while($connection_via_cookie);
        return false;

    }



    public function setToken(){

        
        $this->token = Functions::randomStr(rand(100, 127));
        
        // $this->_query("UPDATE ".DB_PREFIX.$this->uid_table_name." SET token=?, time=?  WHERE uid=?", array($this->token, time(), $this->current_uid), __FILE__, __LINE__);

        return true;
    }

    public function setApp($app){
        $this->userAgent = $app;
    }

    public function saveIp(){
        if(!in_array($_SERVER['REMOTE_ADDR'], $this->ips)){
            $this->ips[] = $_SERVER['REMOTE_ADDR'];
            // $this->_query("UPDATE ".DB_PREFIX.$this->uid_table_name." SET ips=? WHERE uid=?", array(serialize($this->current_ip_list), $this->current_uid), __FILE__, __LINE__);
        }
    }

    public function createSession($cookie = false){

        
        //on crée la session
        $_SESSION[$this->session_name] = serialize(array($this->username, $this->uid, $this->token));
        // $_SESSION[$this->session_connect] = !empty($_COOKIE['PHPSESSID'])? $_COOKIE['PHPSESSID'] : SID;

        //et un cookie au besoin
        if($cookie || !empty($_COOKIE[$this->cookie_name])){
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

    public function set_infos($uid = null){

        
        if(empty($uid)){
            //on enregistre le nouvel uid
            $this->generateUid();
            $this->current_ip = $_SERVER['REMOTE_ADDR'];
            $this->ips = array($_SERVER['REMOTE_ADDR']);
            $this->time = time();
            $this->id = $this->sgdbLastInsertId(); 

        }
        else{
            $this->uid = $uid;
            $this->fillObject("uid");
        }

        $this->setToken();

        return true;
    }

    /**
     * Sets the value of userAgent.
     *
     * @param mixed $userAgent the app infos
     *
     * @return self
     */
    public function setuserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Gets the value of expiration.
     *
     * @return mixed
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Sets the value of expiration.
     *
     * @param mixed $expiration the expiration
     *
     * @return self
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;

        return $this;
    }

     /**
     * Sets the value of username.
     *
     * @param mixed $username the username
     *
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }
     /**
     * Sets the value of userId.
     *
     * @param mixed $userId the userId
     *
     * @return self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Gets the value of username.
     *
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Gets the value of application.
     *
     * @return mixed
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Sets the value of application.
     *
     * @param mixed $application the application
     *
     * @return self
     */
    protected function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Gets the value of app_can_speak.
     *
     * @return mixed
     */
    public function getAppCanSpeak()
    {
        return $this->app_can_speak;
    }

    /**
     * Sets the value of app_can_speak.
     *
     * @param mixed $app_can_speak the app can speak
     *
     * @return self
     */
    protected function setAppCanSpeak($app_can_speak)
    {
        $this->app_can_speak = $app_can_speak;

        return $this;
    }
}