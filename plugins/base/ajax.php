<?php

$GLOBALS['json_returns'] = array("status" => false, "message" => "unknow error");

if(!empty($_)){
    if(!empty($_['page'])){
        switch ($_['page']) {
            //////////////
            //Connexion //
            //////////////
            case 'signin':
                $user = new User();

                if($user->connect($_['user'], $_['pass'], $_['remember_me']))
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
                    // $user->setDashboardList(array(
                    //     array("default", "position" => 0),
                    //     array("actual_users", "position" => 1),
                    //     array("lorem", "position" => 2),
                    // ));
                    // $user->sgbdSave();
                    // /

                    if (!empty($_['dashboard'])) {
                        switch ($_['dashboard']) {
                            //getters
                            case 'get_all':
                                $GLOBALS['json_returns'] = array("status" => true, "dashboard_list" => $user->getDashboardList(), "message" => "ok");
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
                                $GLOBALS['json_returns'] = array('status' => true, "dash_title" => "User actif");
                            break;
                            case 'actual_users':
                                $avatar = '<img src="'.$user->getAvatar().'" alt="avatar de '.$user->getName().'" class="img-thumbnail">';
                                $content = "$avatar <br> ".$user->getName()."";
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
                        $old_list = $user->getDashboardList();
                        $i=0;
                        foreach ($old_list as $key => $value) {
                            $new_list[] = array($old_list[$_['change_order'][$key]], "position" => $i);
                            $i++;
                        }
                        $user->setDashboardList($new_list);
                        $user->sgbdSave();
                    }
                }
            break;

            default:
               $GLOBALS['json_returns']['status'] = false;
            break;
        }

    }



}



echo json_encode($GLOBALS['json_returns']);