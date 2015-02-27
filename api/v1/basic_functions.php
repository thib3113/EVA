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
        "ping" => "pong",
        "version" => PROGRAM_VERSION,
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
    global $RaspberryPi, $myUser, $API;
        if($myUser->is_connect){
            $RaspberryPi->checkUpdate();
            $return = array(
                "status" => true,
                "message" => "success",
                "error_code" => 200,
                );
        }
        else{
            $return = $API["NEED_AUTH"];
        }
}

function API_GET_WIDGET_ALL(&$return){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        $return = array(
                        "status" => true, 
                        "dashboard_list" => $myUser->getDashboardList(),
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

function API_GET_WIDGET_DEFAULT(&$return){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        $return = array(
                        'status' => true,
                        "dash_content" => "Bienvenue sur E.V.A, enjoy !",
                        "dash_width" => 12,
                        "dash_title" => "Widget par défaut",
                        "error_code" => 200,
                        );
    }
    else
        $return = $API["NEED_AUTH"];
}

function API_GET_WIDGET_NETWORK(&$return){
    global $API, $myUser, $_, $system;
    if($myUser->is_connect){
        $return = array(
                        'status' => true, 
                        "dash_title" => "Réseau",
                        "dash_content" => $system->getNetworkInfos() ,
                        "error_code" => 200,
                        );
    }
    else
        $return = $API["NEED_AUTH"];
}

function API_GET_WIDGET_ACTUAL_USERS(&$return){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        $avatar = '<img src="'.$myUser->getAvatar().'" alt="avatar de '.$myUser->getName().'" class="img-thumbnail">';
        $content = "$avatar ".$myUser->getName()."";
        $return = array(
                        "status" => true, 
                        "message" => "ok", 
                        "dash_title" => "User actif", 
                        "dash_content" => $content, 
                        "dash_width" => 4,
                        "error_code" => 200,
                        );
    }
    else
        $return = $API["NEED_AUTH"];
}

function API_GET_WIDGET_LOREM(&$return){
    global $API, $myUser, $_;
    if($myUser->is_connect){
        $return = array(
                        "status" => true,
                        "message" => "ok", "dash_title" => "lorem",
                        "dash_content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam aut sequi nobis corporis veniam voluptatem reiciendis animi necessitatibus fugit! At quos dolor iusto libero. Ullam reiciendis, soluta ea dolore distinctio. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet eaque neque quaerat voluptates obcaecati aspernatur, minima iure quas. Natus ea eius voluptates. Sed iure, iste omnis natus similique quidem fugit",
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
        $myUser->setPass($_["pass"], $myUser->getUsername(), true);
        $myUser->setEmail($_["email"]);
        var_dump($myUser);
        // $myUser->sgdbSave();
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
                    "appInfos"  => (!empty($_SERVER["HTTP_X_APPINFO"])? $_SERVER["HTTP_X_APPINFO"] : "web|".$_SERVER["HTTP_USER_AGENT"] )
                )
            ) 
        );
        $myUser->connect(
            $_['user'],
            $_['pass'],
            $_['remember_me']
        );
        if($myUser->is_connect)
            $return = array("status" => true,  "error_code" => 200, "message" => "Vous êtes connectés");
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
            $myUser->setDashboardList($_["widget_list"]);
            $myUser->sgdbSave();
            $return["status"] = true;
            $return["message"] = "widget add";
            $return["message"] = $_["widget_list"];
            $return["dash"] = $myUser->getDashboardList();
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
            $old_list = $myUser->getDashboardList();
            $i=0;
            foreach ($_['change_order'] as $key => $value) {
                $new_list[$key] = $old_list[$value];
                // echo $value." : ".$old_list[$_['change_order'][$value]]." -> ".$_['change_order'][$value]."\n";
                $i++;
            }
            ksort($new_list);

            $myUser->setDashboardList($new_list);
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

//liste de widget initiaux
Plugin::addHook("API_GET_WIDGET_DEFAULT", "API_GET_WIDGET_DEFAULT", array(&$return), array("title" => "DEFAULT"));
// Plugin::addHook("API_GET_WIDGET_NETWORK", "API_GET_WIDGET_NETWORK", array(&$return), array("title" => "Réseau"));
Plugin::addHook("API_GET_WIDGET_ACTUAL_USERS", "API_GET_WIDGET_ACTUAL_USERS", array(&$return), array("title" => "Users actif"));
Plugin::addHook("API_GET_WIDGET_LOREM", "API_GET_WIDGET_LOREM", array(&$return), array("title" => "Lorem Ipsum"));

Plugin::addHook("API_SET_USER_INFO", "API_SET_USER_INFO", array(&$return));
Plugin::addHook("API_SET_GPIO_STATE", "API_SET_GPIO_STATE", array(&$return));
Plugin::addHook("API_SET_AUTH", "API_SET_AUTH", array(&$return));