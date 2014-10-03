<?php

$GLOBALS['json_returns'] = array("status" => false, "message" => "unknow error");

if(!empty($_)){
    if(!empty($_['page'])){
        switch ($_['page']) {
            case 'signin':
                $user = new User();

                if($user->connect($_['user'], $_['pass'], $_['remember_me']))
                    $GLOBALS['json_returns'] = array("status" => "success","message" => "Vous êtes connectés");
                else
                    $GLOBALS['json_returns'] = array("status" => "error","message" => "Le nom d'utilisateur et/ou le mot de passe est incorrect");
            break;

            case 'index':

                if($myUser){
                    $user->setDashboardList(array(array("active_users", "position" => 0), array("default", "position" => 1) ));
                    $user->sgbdSave();
                    if (!empty($_['dashboard'])) {
                        switch ($_['dashboard']) {
                            case 'get_all':
                                $GLOBALS['json_returns'] = array("status" => true, "dashboard_list" => $user->getDashboardList(), "message" => "ok");
                            break;
                            case 'get_list':
                                $GLOBALS['json_returns'] = array("status" => true, "dashboard_list" => $user->getDashboardList(), "message" => "ok");
                            break;
                            case 'active_users':
                                $avatar = '<img src="'.$user->getAvatar().'" alt="avatar de '.$user->getName().'" class="img-circle">';
                                $content = "$avatar <br> ".$user->getName()."";
                                $GLOBALS['json_returns'] = array("status" => true, "message" => "ok", "dash_title" => "User actif", "dash_content" => $content, "dash_width" => 12);
                            break;
                            case 'default':
                                $GLOBALS['json_returns'] = array("status" => true, "message" => "ok", "dash_title" => "lorem", "dash_content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam aut sequi nobis corporis veniam voluptatem reiciendis animi necessitatibus fugit! At quos dolor iusto libero. Ullam reiciendis, soluta ea dolore distinctio. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet eaque neque quaerat voluptates obcaecati aspernatur, minima iure quas. Natus ea eius voluptates. Sed iure, iste omnis natus similique quidem fugit?Vous n'avez pas encore ajouté de dashboard");
                            break;
                            
                            default:
                            
                            break;
                        }
                    }
                }
            break;
            
            default:
                # code...
                break;
        }
        
    }
    


}



echo json_encode($GLOBALS['json_returns']);