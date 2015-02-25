<?php

function affich_index(){
    global $myUser;

    if($myUser->is_connect){
        Configuration::setTemplateInfos(array("tpl" => __DIR__.'/vues/index/index.tpl'));
        Configuration::addJs('vues/js/jquery-ui.min.js');
        Configuration::addJs('plugins/base/vues/index/js/widget.js');
        Configuration::addJs('plugins/base/vues/index/js/dashboard.js');
        Configuration::addJs('plugins/base/vues/index/js/index.js');

        //affichage
        Plugin::callHook("pre_header");
        Plugin::callHook("header");
        Plugin::callHook("pre_content");
        Plugin::callHook("content");
        Plugin::callHook("pre_footer");
        Plugin::callHook("footer");
    }
}

function affich_json_index(){
    global $myUser, $ajaxResponse, $_, $system;
    if($myUser->is_connect){
        //debuguage uniquement
        // $myUser->setDashboardList(array(
        //     array("default", "position" => 0),
        //     array("actual_users", "position" => 1),
        //     array("lorem", "position" => 2),
        // ));
        // $myUser->sgdbSave();
        // /
        if (!empty($_['dashboard'])) {

            if(!empty($_['new_widget']) && $_['new_widget'] == 1){
                $myUser->addDashboard(array($_['dashboard']));
                $myUser->sgdbSave();
            }
            // var_dump($myUser->dashboard_list);
            switch ($_['dashboard']) {
                //getters
                case 'get_all':
                    $ajaxResponse->set_response(array("status" => true, "dashboard_list" => $myUser->getDashboardList(), "message" => "ok"));
                break;
                case 'get_list':
                    $list_widget = array(
                        array("network", "Réseau"),
                        array("actual_users", "User actuel"),
                        array("lorem", "Lorem ipsum")
                    );
                    $ajaxResponse->set_response(array("status" => true, "widget_list" => $list_widget, "message" => "ok"));
                break;


                //widgets
                case "default":
                    $ajaxResponse->set_response(array('status' => true,"dash_content" => "Bienvenue sur E.V.A, enjoy !", "dash_width" => 12, "dash_title" => "Wiget par défaut"));
                break;
                case 'network':
                    $ajaxResponse->set_response(array('status' => true, "dash_title" => "Réseau","dash_content" => $system->getNetworkInfos() ));
                break;
                case 'actual_users':
                    $avatar = '<img src="'.$myUser->getAvatar().'" alt="avatar de '.$myUser->getName().'" class="img-thumbnail">';
                    $content = "$avatar ".$myUser->getName()."";
                    $ajaxResponse->set_response(array("status" => true, "message" => "ok", "dash_title" => "User actif", "dash_content" => $content, "dash_width" => 4));
                break;
                case 'lorem':
                    $ajaxResponse->set_response(array("status" => true, "message" => "ok", "dash_title" => "lorem", "dash_content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam aut sequi nobis corporis veniam voluptatem reiciendis animi necessitatibus fugit! At quos dolor iusto libero. Ullam reiciendis, soluta ea dolore distinctio. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet eaque neque quaerat voluptates obcaecati aspernatur, minima iure quas. Natus ea eius voluptates. Sed iure, iste omnis natus similique quidem fugit"));
                break;

                default:

                break;
            }
        }

        if(!empty($_['change_order'])){
            $old_list = $myUser->getDashboardList();
            $i=0;
            foreach ($_['change_order'] as $key => $value) {
                $new_list[] = array($old_list[$value], "position" => $key);
                // echo $value." : ".$old_list[$_['change_order'][$value]]." -> ".$_['change_order'][$value]."\n";
                $i++;
            }

            $myUser->setDashboardList($new_list);
            $myUser->sgdbSave();

            $return['status'] = true;
            $return['message'] = "modification réussie";
            $ajaxResponse->set_response($return);
        }
    }
    else
        $ajaxResponse->set_response(array("status" => false, "message" => "Vous n'etes plus connecté"));
}

if($myUser->is_connect)
    Plugin::addHook("header", "Configuration::addMenuItem", array("Accueil", "index","home", 0));