<?php
////////////////////////////////
// Fonctions basique de l'API //
////////////////////////////////

/////////////
// GETTERS //
/////////////

/**
 * permet de Pinguer EVA
 * @author Thibaut SEVERAC (thibaut@thib3113.fr)
 * @return pong et la version d'EVA
 */
function API_GET_PING(&$return){
    $return = array(
        "status" => true,
        "message" => "success",
        "ping" => "pong",
        "version" => PROGRAM_VERSION,
        "error_code" => 200,
    );
    return $return;
}

/**
 * Donne l'état des GPIOS
 * @author Thibaut SEVERAC (thibaut@thib3113.fr)
 * @return array état des GPIO
 */
function API_GET_GPIO_STATE(&$return){
    global $RaspberryPi, $myUser, $API;
    if($myUser->is_connect){
        $return = array(
            "status" => true,
            "message" => "success",
            "error_code" => 200,
            "GPIO" => $RaspberryPi->getAllState(),
            );
    }
    else
        $return = $API["NEED_AUTH"];

    return $return;
}

/**
 * Cherche des mises à jour de EVA
 * @author Thibaut SEVERAC (thibaut@thib3113.fr)
 * @return ???
 */
function API_GET_CHECK_UPDATE(&$return){
    global $API, $myUser, $_;
        if(!$myUser->is_connect){
            $return = $API["NEED_AUTH"];
            return $return;
        }

    $curl_handle=curl_init();
    curl_setopt($curl_handle, CURLOPT_URL,'https://raw.githubusercontent.com/thib3113/EVA/dev/static.php');
    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Evaproject.net get version');
    $dev_file = curl_exec($curl_handle);
    curl_close($curl_handle);
    preg_match("~define\('PROGRAM_VERSION','([0-9.]+)'\);~", $dev_file, $matches);
    if(!empty($matches[1]))
      $developpeur_version = $matches[1];

        $return = array(
        "status" => true,
        "message" => "success",
        "error_code" => 200,
        "dev_version" => $developpeur_version,
        );
        return $return;
}

function API_GET_WIDGET_ALL(&$return){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        $return = array(
                        "status" => true, 
                        "dashboard_list" => $myUser->getWidgetList(),
                        "message" => "ok",
                        "error_code" => 200,
                        );
    }
    else
        $return = $API["NEED_AUTH"];
}

function API_GET_WIDGET_LIST(&$return){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        $list_widget_temp = Plugin::getWidgetList();
        $list_widget = array();
        foreach ($list_widget_temp as $key => $value) {
                $list_widget[] = array(
                                        substr($key, strlen("API_GET_WIDGET_")),
                                        (empty($value["title"])? $key : $value["title"]),
                                    );
        }
        $return = array(
                        "status" => true, 
                        "widget_list" => $list_widget,
                        "message" => "ok",
                        "error_code" => 200,
                        );
    }
    else
        $return = $API["NEED_AUTH"];
}

function API_GET_WIDGET_DEFAULT(&$return, $default_width){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        $return = array(
                        'status' => true,
                        "dash_content" => "Bienvenue sur E.V.A, enjoy !",
                        "dash_width" => (!empty($myUser->getWidget("DEFAULT")["width"])?$myUser->getWidget("DEFAULT")["width"] : $default_width),
                        "dash_title" => "Widget par défaut",
                        "error_code" => 200,
                        );
    }
    else
        $return = $API["NEED_AUTH"];
}

function API_GET_WIDGET_NETWORK(&$return, $default_width){
    global $API, $myUser, $_, $system;
    if($myUser->is_connect){
        $return = array(
                        'status' => true, 
                        "dash_title" => "Réseau",
                        "dash_content" => $system->getNetworkInfos() ,
                        "dash_width" => (!empty($myUser->getWidget("NETWORK")["width"])?$myUser->getWidget("NETWORK")["width"] : $default_width),
                        "error_code" => 200,
                        );
    }
    else
        $return = $API["NEED_AUTH"];
}

function API_GET_WIDGET_ACTUAL_USERS(&$return, $default_width){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        $avatar = '<img src="'.$myUser->getAvatar().'" alt="avatar de '.$myUser->getName().'" class="img-thumbnail">';
        $content = "$avatar ".$myUser->getName()."";
        $return = array(
                        "status" => true, 
                        "message" => "ok", 
                        "dash_title" => "User actif", 
                        "dash_content" => $content, 
                        "dash_width" => (!empty($myUser->getWidget("ACTUAL_USERS")["width"])?$myUser->getWidget("ACTUAL_USERS")["width"] : $default_width),
                        "error_code" => 200,
                        );
    }
    else
        $return = $API["NEED_AUTH"];
}

function API_GET_WIDGET_LOREM(&$return, $default_width){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        $return = array(
                        "status" => true,
                        "message" => "ok",
                        "dash_title" => "lorem",
                        "dash_content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam aut sequi nobis corporis veniam voluptatem reiciendis animi necessitatibus fugit! At quos dolor iusto libero. Ullam reiciendis, soluta ea dolore distinctio. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet eaque neque quaerat voluptates obcaecati aspernatur, minima iure quas. Natus ea eius voluptates. Sed iure, iste omnis natus similique quidem fugit",
                        "dash_width" => (!empty($myUser->getWidget("LOREM")["width"])?$myUser->getWidget("LOREM")["width"] : $default_width),
                        "error_code" => 200,
                        );
    }
    else
        $return = $API["NEED_AUTH"];
}


/////////////
// SETTERS //
/////////////

/**
 * Change l'état du GPIO (int) $_["GPIO"] à l'état (bool) $_["GPIO_STAT"]
 * @author Thibaut SEVERAC thibaut@thib3113.fr
 */
function API_SET_GPIO_STATE(&$return){
        global $API, $myUser, $_;
        if(!$myUser->is_connect){
            return $API["NEED_AUTH"];
        }
        if((empty($_["GPIO"]) && intval($_["GPIO"]) !== 0) || (empty($_["GPIO_STATE"]) && intval($_["GPIO_STATE"]) !== 0) ){
            $return = array(
                "status" => false,
                "message" => "parametre manquant",
                "error_code" => 400,
                );
        }
        elseif(!is_int((int)$_["GPIO_STATE"]) || !is_int((int)$_["GPIO"])){

            $return = array(
                "status" => false,
                "message" => "parametre incorrects",
                "error_code" => 405,
                );
        }
        else{
            $RaspberryPi->write($_["GPIO"], $_["GPIO_STATE"], true);

            $return = array(
                "status" => true,
                "message" => "success",
                "error_code" => 200,
                "wiringpin" => $_["GPIO"],
                "state" => $RaspberryPi->read($_["GPIO"]),
                );

        }
        return $return;
}

/**
 * Change les informations de l'utilisateur
 * @author Thibaut SEVERAC (thibaut@thib3113.fr)
 * @param  username username
 * @param  pass     mot de passe
 * @param  email    email
 */
function API_SET_USER_INFO(&$return){
    global $API, $myUser, $_;
        if(!$myUser->is_connect){
            $return = $API["NEED_AUTH"];
            break;
        }
        $myUser->setUsername($_["username"]);
        // var_dump($myUser->getPass());
        $myUser->setPass($_["pass"], $myUser->getUsername(), true);
        // var_dump($myUser->getPass());
        $myUser->setEmail($_["email"]);
        // var_dump($myUser);
        $myUser->sgdbSave();
        $return = array(
            "status" => true,
            "message" => "success",
            "error_code" => 200,
            );
        return $return;

}

/**
 * Permet de crée une connection
 * @author Thibaut SEVERAC (thibaut@thib3113.fr)
 * @param user          nom de compte
 * @param pass          mot de passe
 * @param remember_me   se souvenir de moi
 */
function API_SET_AUTH(&$return){
    global $API, $myUser, $_;
    // var_dump($_);
    if($myUser->is_connect){
        $return["status"] = false;
        $return["message"] = "deja connecte";
        $return["error_code"] = 200;
        return;   
    }
    if(!empty($_['user']) && !empty($_['pass'])){
        $_['remember_me'] = empty($_['remember_me'])? false : $_['remember_me'];
        $myUser = new User(
            array(
                "ConnectionOptions" => array(
                    "expiration" => (!empty($_["expire"])? $_["expire"] : time() ),
                    "userAgent"  => $_SERVER["HTTP_USER_AGENT"]
                )
            ) 
        );
        $myUser->connect(
            $_['user'],
            $_['pass'],
            $_['remember_me']
        );
        if($myUser->is_connect){
            $texte = "Vous êtes connectés";
            if(!empty($_["can_speak"])){
                $texte = (date("H")>7 && date("H")< 18? "Bonjour" : "Bonsoir").", ".$myUser->getShortName()." . ";
                $curl_handle=curl_init();
                curl_setopt($curl_handle, CURLOPT_URL,'http://rss.accuweather.com/rss/liveweather_rss.asp?metric=1&locCode=EUR|FR|FR016|TOULOUSE');
                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLOPT_USERAGENT, 'EVA get weather http://evaproject.net');
                $weather_file = curl_exec($curl_handle);
                preg_match("~currently in ([a-z]*),\s*[^0-9]*([0-9]++)(?:&#176;|\s)*C\s*and\s*([a-z]++)~Uis", $weather_file, $matches);
                $town = $matches[1];
                $temp = $matches[2];
                $weather = $matches[3];

                if($temp < 10)
                    $texte .= "Il semblerais qu'il fasse froid, $temp °C à $town pour le moment.";
                elseif($temp > 30)
                    $texte .= "Il semblerais qu'il fasse chaud, restez au frais . ";

                if(preg_match("~rain|shower~uis", $weather))
                    $texte .= "Je pense qu'il va pleuvoir à $town, pensez à vous couvrir .";
            }
            $return = array("status" => true,  "error_code" => 200, "message" => $texte, "cookies" => json_encode($_COOKIE));
        }
        else
            $return = array("status" => false, "error_code" => 401, "message" => "Le nom d'utilisateur et/ou le mot de passe est incorrect");
    }
    else{
        $return["message"] = "parametre incorrects";
        $return["error_code"] = 400;
    }
}

function API_SET_WIDGET_LIST(&$return){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        if(!empty($_["widget_list"])){
            $myUser->setWidgetList($_["widget_list"]);
            $myUser->sgdbSave();
            $return["status"] = true;
            $return["message"] = "widget add";
            $return["message"] = $_["widget_list"];
            $return["dash"] = $myUser->getWidgetList();
            $return["error_code"] = 200;
        }
        else{
            $return["message"] = "parametre incorrects";
            $return["error_code"] = 400;
        }
    }
    else
        $return = $API["NEED_AUTH"];
}

function API_SET_WIDGET_ORDER(&$return){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        if(!empty($_["change_order"])){
            $old_list = $myUser->getWidgetList();
            $i=0;
            foreach ($_['change_order'] as $key => $value) {
                $new_list[$key] = $old_list[$value];
                // echo $value." : ".$old_list[$_['change_order'][$value]]." -> ".$_['change_order'][$value]."\n";
                $i++;
            }
            ksort($new_list);

            $myUser->setWidgetList($new_list);
            $myUser->sgdbSave();

            $return['status'] = true;
            $return['message'] = "modification réussie";
            $return["error_code"] = 200;
        }
        else{
            $return["message"] = "parametre incorrects";
            $return["error_code"] = 400;
        }        
    }
    else
        $return = $API["NEED_AUTH"];

}

function API_SET_WIDGET_WIDTH(&$return){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        if(!empty($_["new_width"]) && !empty($_["widget_name"])){
            $myUser->setWidgetSize($_["widget_name"], $_["new_width"]);
            $myUser->sgdbSave();

            $return['status'] = true;
            $return['message'] = "mise à jour de la taille réussie";
            $return["error_code"] = 200;
        }
        else{
            $return["message"] = "parametre incorrects";
            $return["error_code"] = 400;
        }
    }
    else
        $return = $API["NEED_AUTH"];

}


/////////////////////////////////////////////
// Ajout des fonctions ci dessus aux hooks //
/////////////////////////////////////////////

Plugin::addHook("API_GET_PING", "API_GET_PING", array(&$return));
Plugin::addHook("API_GET_GPIO_STATE", "API_GET_GPIO_STATE", array(&$return));
Plugin::addHook("API_GET_CHECK_UPDATE", "API_GET_CHECK_UPDATE", array(&$return));

//get widget
Plugin::addHook("API_GET_WIDGET_ALL", "API_GET_WIDGET_ALL", array(&$return));
Plugin::addHook("API_GET_WIDGET_LIST", "API_GET_WIDGET_LIST", array(&$return));
Plugin::addHook("API_SET_WIDGET_LIST", "API_SET_WIDGET_LIST", array(&$return));
Plugin::addHook("API_SET_WIDGET_ORDER", "API_SET_WIDGET_ORDER", array(&$return));
Plugin::addHook("API_SET_WIDGET_WIDTH", "API_SET_WIDGET_WIDTH", array(&$return));

//liste de widget initiaux
Plugin::addHook("API_GET_WIDGET_DEFAULT", "API_GET_WIDGET_DEFAULT", array(&$return, "width" => 8), array("title" => "DEFAULT"));
// Plugin::addHook("API_GET_WIDGET_NETWORK", "API_GET_WIDGET_NETWORK", array(&$return, "width" => 4), array("title" => "Réseau"));
Plugin::addHook("API_GET_WIDGET_ACTUAL_USERS", "API_GET_WIDGET_ACTUAL_USERS", array(&$return, "width" => 4), array("title" => "Users actif"));
Plugin::addHook("API_GET_WIDGET_LOREM", "API_GET_WIDGET_LOREM", array(&$return, "width" => 8), array("title" => "Lorem Ipsum"));

Plugin::addHook("API_SET_USER_INFO", "API_SET_USER_INFO", array(&$return));
Plugin::addHook("API_SET_GPIO_STATE", "API_SET_GPIO_STATE", array(&$return));
Plugin::addHook("API_SET_AUTH", "API_SET_AUTH", array(&$return));