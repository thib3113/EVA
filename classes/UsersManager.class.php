<?php

class UsersManager extends SgdbManager{
    protected $TABLE_NAME = "User";

    private $table_users = "Users"; //nom de la table contenant les users
    private $col_users = "name"; //nom de la colonne contenant les noms d' users
    private $col_pass = "pass"; //nom de la colonne contenant les mot de pass
    private $hash = "md5"; //methode de hashage du password
    private $type_of_error = "bol"; // bol | int | text | array possible
    private $session_name = "connect"; // nom de la session crée pour la connection
    private $cookie_name = "connect"; // nom de la session crée pour la connection
    private $cookie_time = 0 ; //cookie de 0 car il est initialisé dans le construct

    //private key
    private $private_key = ""; //clef privée
    private $enable_private_key = false; //active/désactive la private key

    //administration
    private $enable_admin = true; // active les admins
    private $col_groups = "group_id"; //nom de la colonne du groupe
    private $group_id_admin = 0; //valeur du groupe si l'utilisateur es admin
    private $group_base = 1;//valeur de groupe par défault
    
    //gravatar
    private $gravatar_enable = false; // attrape t'on le gravatar
    private $gravatar_size = 80; // 1 - 2048 definie la taille de l'image
    private $gravatar_default = 404; // image par défault de l'image [ 404 | mm | identicon | monsterid | wavatar ]
    private $gravatar_max_rat = 'g'; // maximum rating ( aucune idée de à quoi ça sert )

    //variable raccourcis
    public $enable_easy_variable = true; //on active les easy_variable
    public $is_admin = false; // l'utilisateur est il un administrateur
    public $is_connect = false; // l'utilisateur est il connecté

    function __construct($config = array()){

        parent::__construct();

        $this->cookie_time = time()+60*60*365*100; //on met le temps du cookie à 10ans

        foreach ($config as $key => $value) { //on met les configs dans les variables correspondantes
            $this->$key = $value;
        }

        //vérification de l'existence de la col users
        if(!SgdbManager::exist_table($this->table_users)){
            $this->setTypeOfError("text");
            die($this->error(4040, true));
        }

        if(@is_null($_SESSION)) // vérification de la déclaration des sessions
            return $this->error(503, true);
    }

    /**
     * Function for add one user on bdd
     * @param array $user_infos the informations of the user, he nedd have col username and password
     */
    public function addUser($user_infos = array()){
        if(!isset($user_infos[$this->col_users]) || !isset($user_infos[$this->col_pass])){
            return $this->error(3);
        }

        if($this->userExist(array($this->col_users => $user_infos[$this->col_users], 'active' => 1))){
            return $this->error(4);
        }

        if($user_infos[$this->col_groups] == ""){
            $user_infos[$this->col_groups] == $this->group_base;
        }

        $user_infos[$this->col_pass] = $this->preparePasswd($user_infos[$this->col_users], $user_infos[$this->col_pass]);

        $keys = array(); //tableau pour stocker les keys
        $values = array(); //tableau pour stocker les values
        //on crée la requete
        foreach ($user_infos as $key => $value) {
            $keys[] = $key;
            $values[] = SgdbManager::escapeString($value);
        }

        //création de la query
        $query = 'INSERT INTO `'.$this->table_users.'` ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).' )';
        if(!SgdbManager::_query($query))
            return $this->error(1);
        else
            return true;
    }

    /**
     * Function for edit one user
     * @param  int $id_user    the id of the user when you will edit information
     * @param  array  $user_infos new informations
     * @return bol             
     */
    public function editUser(int $id_user, $user_infos = array()){
        
        if(!$this->userExist(array('id' => $id_user))){
            return $this->error(4044);
        }

        if(isset($user_infos[$this->col_pass]))
            $user_infos[$this->col_pass] = $this->preparePasswd($user_infos($this->col_pass));

        $values = array(); //tableau pour stocker les values
        //on crée la requete
        foreach ($user_infos as $key => $value) {
            $values[] = $key.'='.SgdbManager::escapeString($value);
        }

        //création de la query
        $query = 'UPDATE '.$this->table_users.' SET '.implode(', ', $values).' WHERE id='.$id_user;
        if(!SgdbManager::_query($query))
            return $this->error(2);
        else
            return true;
    }

    /**
     * Function for remove one user
     * @param  int    $id_user id of the user
     * @return bol    
     */
    public function removeUser(int $id_user){
        
        if(!$this->userExist(array('id' => $id_user))){
            return $this->error(4044);
        }

        //création de la query
        $query = 'DELETE FROM '.$this->table_users.' WHERE id='.$id_user;
        if(!SgdbManager::_query($query))
            return $this->error(2);
        else
            return true;
    }

    /**
     * Get user info
     * @param  int    $id id of user
     * @return array  all informations
     */
    function getUserInfos($id){

        if(!$this->userExist(array('id' => $id))){
            return array(
                                        'username' => 'utilisateur supprimé',
                                        'avatar'   => NULL,
                                        'group_id' => 3,
                                        'email'    => '',
                                        'active'   => 0,
                                        'id'       => 0 
                                        );
            // return $this->error(4044);
        }

        //création de la query
        $query = 'SELECT * FROM '.$this->table_users.' WHERE id='.$id;
        $result_ = SgdbManager::_query($query);
        $return = SgdbManager::fetch_assoc($result_);
        $return['avatar'] = empty($return['avatar'])? $this->getGravatar($return['email']) : $config['base_url'].'/vues/img_up/profils/'.$return['avatar'];
        return $return; 
        
    }

    /**
     * Verify if the user existe
     * @param  array   $user_infos user infos
     * @param  boolean $details    will you all information or just yes no ?
     * @param  boolean $each       verify user exist for each information or just with full information
     * @return array/bol           return bol or details
     */
    public function userExist($user_infos = array(), $details = false, $each = false){

        if($each){
            $result_ = array();
            foreach ($user_infos as $key => $value) {
                $query = 'SELECT * FROM '.$this->table_users.' WHERE `'.$key.'` = '.SgdbManager::escapeString($value);
                $result_query = SgdbManager::_query($query);
                $result = SgdbManager::fetch_assoc($result_query);
                if(!empty($result)){
                    if(!$details)
                        return true;
                    else
                        $result_[$key] = $result;
                }
            }
            if(!empty($result_))
                return $result_;
            else
                return false;
        }
        else{
            $values = array(); //tableau pour stocker les values
            //on crée la requete
            foreach ($user_infos as $key => $value) {
                $values[] = $key.'='.SgdbManager::escapeString($value);
            }

            //création de la query
            $query = 'SELECT * FROM '.$this->table_users.' WHERE '.implode(' AND ', $values);
            $result_query = SgdbManager::_query($query);
            $result = SgdbManager::fetch_assoc($result_query);

            if(!empty($result)){
                if(!$details)
                    return true;
                else
                    return $result;
            }
            else
                return false;
        }        
    }

    /**
     * recup error information
     * @param  int  $number    number of this error
     * @param  boolean $fatal  this error is fatal and do stop the script ?
     * @return $type_of_error  return what you need
     */
    public function error($number, $fatal = false){

        switch($number){
            case 1 :
                $error = "erreur lors de la création de l'utilisateur";
            break;
            case 2:
                $error = "erreur lors de la modification de l'utilisateur";
            break;
            case 3:
                $error = "l'une des valeur de obligatoire à la création d'un utilisateur est inexistante ( les valeurs obligatoire sont nom de compte et mot de passe )";
            break;
            case 4:
                $error = "l'utilisateur existe déjà";
            break;
            case 5:
                $error = "l'utilisateur et/ou le mot de passe est incorrect !";
            break;
            case 4040 :
                $error = "la table des utilisateurs est inexistante";
            break;
            case 4041 :
                $error = "la colonne des nom d'utilisateurs est inexistante";
            break;
            case 4042 :
                $error = "la colonne des mot de passe est inexistante";
            break;
            case 4043 :
                $error = "la colonne des groupe est inexistante";
            break;
            case 4044 :
                $error = "l'utilisateur n'existe pas";
            break;
            case 503 :
                $error = "la variable \$_SESSION n'est pas accessible";
            break;
            default:
                $error = "erreur inconnue";
            break;
        }

        if($fatal === true){
            die($error);
        }
        else{
            //on regarde la façon dont l'utilisateur veux ses erreurs
            switch ($this->type_of_error) {
                case 'bol':
                    return false;
                break;
                case 'int':
                    return $number;
                break;
                case 'text':
                    return $error;
                break;
                case 'array':
                    $return = array('bol' => false, 'int' => $number, 'text' => $error);
                    return $return;
                break;
            }
        }
    }

    /**
     * hash the password
     * @param  str      $username   the username
     * @param  str      $pass       the password
     * @return str                  the password encrypted
     */
    public function preparePasswd($username, $pass){ //on prépare le mot de passe à un stockage en bdd
        if($this->enable_private_key)
            $pass_temp = $username.$this->private_key.$pass;
        else
            $pass_temp = $username.$pass;

        if($this->hash)
            $pass_temp = hash($this->hash, $pass_temp);

        return $pass_temp;
    }

    public function connect($user, $password, $remember_me = false, $need_encrypt = true){ 
        $password = ($need_encrypt)? $this->preparePasswd($user, $password) : $password; //on prépare le mot de passe si celui ci n'as pas était hashé auparavant 

        //on fait une requete du password avec le mot de passe
        $result_query = SgdbManager::_query('SELECT * FROM '.$this->table_users.' WHERE `'.$this->col_users.'`='.SgdbManager::escapeString($user).' AND `'.$this->col_pass.'`='.SgdbManager::escapeString($password));
        $result = SgdbManager::fetch_assoc($result_query);
        
        if(empty($result)) // si cela ne retourne rien, c'est que le mot de passe ne correspond pas à cet identifiant 
            return $this->error(5);
        else{
            //on crée une session à partir du mot de passe hashé et du nom de compte
            $_SESSION[$this->session_name] = serialize(array($user, $password));
            if($remember_me)// si on demande de se souvenir, on crée un cookie
                setcookie($this->cookie_name, serialize( array($user, $password) ), time()+$this->cookie_time, '/' );
            return new User($result['id']);
        }


    }

    public function disconnect()
    {
        $_SESSION[$this->session_name] = "";
        $_COOKIE[$this->cookie_name] = "";
        setcookie($this->cookie_name);
        return true;

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
                if($this->enable_easy_variable){ //si les globales son activés
                    if($this->enable_admin && $user[$this->col_groups] == $this->group_id_admin) //si les admins sont activés, et que l'utilisateur est un admin
                        $GLOBALS['is_admin'] = true; //on crée une variable $is_admin qui sortira
                    else
                        $GLOBALS['is_admin'] = false;
                }
                $GLOBALS['is_connect'] = true;//on crée la variable $is_connect
                return $user;
            }
            else
                return false;
        }
        else
            return false; //si la session n'existe pas, on retourne faux
    }

    public function getProfileLink($user_id){
        global $functions, $config;

        $user_infos = $this->getUserInfos($user_id);
        return $config['base_url'].'/users/'.$user_infos['id'].'/'.$functions->clean_url($user_infos['username']).'.html';

    }

    /*******************
        SETTER
    *******************/
    
    /**
     * set type of error you will
     * @param str $error_type  bol | int | text | array
     */
    public function setTypeOfError($error_type){ // on permet la modification du type d'erreur
        $this->type_of_error = $error_type;
    }

    public function setHash($hash){ // on permet la modification du type de hash
        if(in_array($hash, hash_algos())){
            $this->hash = $hash;
            return true;
        }
        else{
            $this->hash = false;
            return false;
        }
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

    public function getGravatar($email) {
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