<?php

$GLOBALS['json_returns'] = array("status" => false, "message" => "unknow error");

if(!empty($_)){
    if(!empty($_['page'])){
        switch ($_['page']) {
            //////////////
            //Connexion //
            //////////////
            case 'signin':
                $myUser = new User($_['user'], $_['pass'], $_['remember_me']);
                if($myUser->is_connect)
                    $GLOBALS['json_returns'] = array("status" => "success","message" => "Vous êtes connectés");
                else
                    $GLOBALS['json_returns'] = array("status" => "error","message" => "Le nom d'utilisateur et/ou le mot de passe est incorrect");
            break;

            //////////////
            //Dashboard //
            //////////////
            case 'dashboard':

                if($myUser){
                    //debuguage uniquement
                    // $myUser->setDashboardList(array(
                    //     array("default", "position" => 0),
                    //     array("actual_users", "position" => 1),
                    //     array("lorem", "position" => 2),
                    // ));
                    // $myUser->sgbdSave();
                    // /
                    if (!empty($_['dashboard'])) {

                        if(!empty($_['new_widget']) && $_['new_widget'] == 1){
                            $myUser->addDashboard(array($_['dashboard']));
                            $myUser->sgbdSave();
                        }
                        switch ($_['dashboard']) {
                            //getters
                            case 'get_all':
                                $GLOBALS['json_returns'] = array("status" => true, "dashboard_list" => $myUser->getDashboardList(), "message" => "ok");
                            break;
                            case 'get_list':
                                $list_widget = array(
                                    array("network", "Réseau"),
                                    array("actual_users", "User actuel"),
                                    array("lorem", "Lorem ipsum")
                                );
                                $GLOBALS['json_returns'] = array("status" => true, "widget_list" => $list_widget, "message" => "ok");
                            break;


                            //widgets
                            case "default":
                                $GLOBALS['json_returns'] = array('status' => true,"dash_content" => "Bienvenue sur E.V.A, enjoy !", "dash_width" => 12, "dash_title" => "Wiget par défaut");
                            break;
                            case 'network':
                                $GLOBALS['json_returns'] = array('status' => true, "dash_title" => "Réseau","dash_content" => $system->getNetworkInfos() );
                            break;
                            case 'actual_users':
                                $avatar = '<img src="'.$myUser->getAvatar().'" alt="avatar de '.$myUser->getName().'" class="img-thumbnail">';
                                $content = "$avatar <br> ".$myUser->getName()."";
                                $GLOBALS['json_returns'] = array("status" => true, "message" => "ok", "dash_title" => "User actif", "dash_content" => $content, "dash_width" => 4);
                            break;
                            case 'lorem':
                                $GLOBALS['json_returns'] = array("status" => true, "message" => "ok", "dash_title" => "lorem", "dash_content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam aut sequi nobis corporis veniam voluptatem reiciendis animi necessitatibus fugit! At quos dolor iusto libero. Ullam reiciendis, soluta ea dolore distinctio. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet eaque neque quaerat voluptates obcaecati aspernatur, minima iure quas. Natus ea eius voluptates. Sed iure, iste omnis natus similique quidem fugit");
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
                        $myUser->sgbdSave();

                        $GLOBALS['json_returns']['status'] = true;
                        $GLOBALS['json_returns']['message'] = "modification réussie";
                    }
                }
                else
                    $GLOBALS['json_returns'] = array("status" => false, "message" => "Vous n'etes plus connecté");
            break;

            default:
               $GLOBALS['json_returns']['status'] = false;
            break;
        }

    }



}



echo json_encode($GLOBALS['json_returns']);