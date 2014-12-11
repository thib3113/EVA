<?php
define('ROOT', __DIR__);

session_start();
$start=microtime(true);

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require ROOT.'/config.php';


function autoload($name) {  
    if (file_exists(ROOT.'/classes/'.$name.".class.php")) { 
        require_once(ROOT.'/classes/'.$name.".class.php"); 
    } 
    else
        return false;
} 

spl_autoload_register("autoload");


$RaspberryPi = new RaspberryPi();
$_ = array_merge($_GET, $_POST);

//on regarde si la db existe déjà, pour empécher l'installaion dans ce cas
if(is_file(DB_NAME)){
    //pour le debug, on supprime le fichier de db à chaque
    // unlink(DB_NAME);
    die('<meta charset="utf-8">le fichier de base de donnée existe déjà <a href="index.php">Retour à l\'accueil</a>');
}

$smarty = new Smarty(); 
$smarty->template_dir = ROOT.'/cache/templates/';
$smarty->compile_dir = ROOT.'/cache/templates_c/';
$smarty->config_dir = ROOT.'/cache/configs/';
$smarty->cache_dir = ROOT.'/cache/cache/';

$taskList = array();
$erreurs = array();
$notices = array();
$error_form = array(
        "username"     => 0,
        "pass"         => 0,
        "pass_confirm" => 0,
        "email"        => 0
    );

////////////////////////////////////////
//on vérifie les droits du dossier db //
////////////////////////////////////////

function createError($error, $resolve = array()){
    global $erreurs;
    $resolve = !is_array($resolve)? array($resolve) : $resolve;

    $return  = $error;
    if(!empty($resolve)){
        $return .= ' <i onclick="$(\'#resolve_'.count($erreurs).'\').toggle(200);" title="cliquez pour afficher une solution possible" class="fa fa-question-circle cursor_pointer"></i>';
        $return .= '<div style="display:none;" id="resolve_'.count($erreurs).'">solution'.(count($resolve)>1? 's' : '').' : <ul>';
        foreach ($resolve as $solution) {
            $return .= '<li>'.$solution.'</li>';
        }
        $return .= '</ul></div>';
    }
    return $return;
}

function check($value){
    if(!$value)
        $GLOBALS['error'] = 1;
    return '[ '.($value? '<span style="color:green">OK</span>' : '<span style="color:red">ERREUR</span>').' ]';
}

$GLOBALS['error'] = 0;
$all_is_not_good_message = "";
$distribution = $RaspberryPi->getInfos("distribution");
$version = $RaspberryPi->getInfos("version");

//on test les erreurs
if(!is_writable(dirname(DB_NAME))){
    $erreurs[] = createError('le dossier '.basename(dirname(DB_NAME)).' n\'est pas disponible en écriture', array("Rendre le dossier inscriptible par tout le monde <kbd>sudo chmod -R 777 ".basename(dirname(DB_NAME))."/</kbd>") );
}

if(!is_writable(ROOT.'/'.PLUGIN_DIR))
    $erreurs[] = createError('le dossier '.PLUGIN_DIR.' n\'est pas disponible en écriture', array("Rendre le dossier inscriptible par tout le monde <kbd>sudo chmod -R 777 ".PLUGIN_DIR."/</kbd>" ));

if(!$RaspberryPi->getInfos('wiringpi'))
    $erreurs[] = createError("WiringPi ne semble pas être installer sur votre RaspberryPi", 'Suivre les étapes d\'installation : <a href="http://wiringpi.com/download-and-install/">http://wiringpi.com/download-and-install/</a>');

if(!$RaspberryPi->getInfos('git'))
    $erreurs[] = createError("Git ne semble pas être installer sur votre RaspberryPi", 'Installer git <kdb>sudo apt-get install git</kdb>');


//on teste les erreurs non blocantes
if(!Functions::checkConnectivity()){
    $notices[] = createError("votre RaspberryPi ne semble pas relié à internet, il nous es donc impossible de voir si votre version est supportée", array("Connecter votre RaspberryPi à internet"));   
}
else{
    if($supportedVersion = Functions::getSupportedVersion()){
        if(!Functions::myVersionIsSupport())
            $notices[] = createError('Votre version ne semble pas faire partie des versions supportées, plus d\'informations sur le site <a href="'.PROGRAM_FORUM.'">'.PROGRAM_FORUM.'</a> (votre version : '.$distribution.' '.$version.')');
    }
    else{
        $notices[] = createError("votre RaspberryPi n'arrive pas à communiquer avec notre site, nous ne pouvons pas voir si votre version est supportée");
    }
}

if(!Functions::isApache()){
    $notices = createError("Il semble que vous n'utilisez pas un serveur apache, le dossier db est donc visible si vous décidez de le rendre accéssible de l'extérieur !", array('Renseigner vous sur notre forum sur des solutions alternatives ( <a href="'.PROGRAM_FORUM.'">'.PROGRAM_FORUM.'</a> )'));
}


//on vérifie les envois de form
if(!empty($_['launch_install'])){
    if(empty($_['username'])){
        $erreurs[] = createError("L'username ne peux pas être vide");
        $error_form['username'] = 1;
    }

    if(empty($_['pass'])){
        $erreurs[] = createError("Le mot de passe ne peux pas être vide");
        $error_form['pass'] = 1;
    }
    if(empty($_['pass_confirm'])){
        $erreurs[] = createError("La confirmation du mot de passe ne peux pas être vide");
        $error_form['pass_confirm'] = 1;
    }
    if( $_['pass'] != $_['pass_confirm']){
        $erreurs[] = createError("La confirmation du mot de passe et le mot de passe ne sont pas identiques");
        $error_form['pass'] = 1;
        $error_form['pass_confirm'] = 1;
    }
    if(empty($_['email'])){
        $erreurs[] = createError("L'email ne peux pas être vide");
        $error_form['email'] = 1;
    }
    if(!filter_var($_['email'], FILTER_VALIDATE_EMAIL)){
        $erreurs[] = createError("L'email ne peux pas être vide");
        $error_form['email'] = 1;        
    }


    if(empty($erreurs)){
        $config = new Configuration();
        $taskList[] = "création de la base ... ".check($config->sgbdCreate());
        $taskList[] = "Ajout des infos ... ".check( $config->addConfig("base_url", 'http://'.$_SERVER['SERVER_NAME']) );

        $taskList[] = "Vérification de la création de la database ... ".check(filesize(DB_NAME) >1);

        $user = new User();
        $taskList[] = "création de la table User ... ".check($user->sgbdCreate());
        $taskList[] = "Création de l'utilisateur ".$_['username'].' ... '.check($user->createUser($_['username'], $_['pass'], $_['email'], 0));
        if($GLOBALS['error']){
            if(!$createBackup = Functions::backupDb())
                $all_is_not_good_message = "Une erreur est intervenue, un backup de la base de donnée à était crée : ".basename($createBackup);
            else
                $all_is_not_good_message = "Une erreur est intervenue mais nous n'avons pas pu crée de backup de la base de donnée. Celà peut être du à un problème de droits.";
        }
        
    }

}
$template_infos = array(
            "title"        => 'Installation - '.PROGRAM_NAME.' '.PROGRAM_VERSION,
            "externjs"     => '',
            "distribution" => $RaspberryPi->getInfos("distribution"),
            "version"      => $RaspberryPi->getInfos("version")
            );
$smarty->assign("all_is_good", !$GLOBALS['error']);
$smarty->assign("all_is_not_good_message", $all_is_not_good_message);
$smarty->assign("error_form", $error_form);
$smarty->assign("taskList", $taskList);
$smarty->assign("erreurs", $erreurs);
$smarty->assign("notices", $notices);
$smarty->assign("template_infos", $template_infos);
$smarty->assign('executionTime',Functions::getExecutionTime());
$smarty->assign('debugList',Functions::getDebugList());
$smarty->display(ROOT."/vues/install.tpl");