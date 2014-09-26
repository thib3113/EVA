<?php
define('ROOT', __DIR__);
session_start();
$start=microtime(true);

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$GLOBALS['debugItems'] = array();

require ROOT.'/config.php';

function autoload($name) {  
    if (file_exists(ROOT.'/classes/'.$name.".class.php")) { 
        require_once(ROOT.'/classes/'.$name.".class.php"); 
    } 
    else
        return false;
} 

spl_autoload_register("autoload");


$_ = array_merge($_GET, $_POST);

$smarty = new Smarty(); 
$smarty->template_dir = ROOT.'/cache/templates/';
$smarty->compile_dir = ROOT.'/cache/templates_c/';
$smarty->config_dir = ROOT.'/cache/configs/';
$smarty->cache_dir = ROOT.'/cache/cache/';

$taskList = array();
$erreurs = array();
$notices = array();

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


$distribution = RaspberryPi::getInfos("distribution");
$version = RaspberryPi::getInfos("version");

//on test les droits
if(is_file(ROOT.'/'.DB_NAME)){
    if(!is_writable(ROOT.'/'.DB_NAME)){
        $erreurs[] = createError('le fichier '.DB_NAME.' existe mais n\'est pas disponible en écriture', array("Rendre le fichier inscriptible par tout le monde <kbd>sudo chmod 777 ".DB_NAME."</kbd>"));
    }  
}
if(!is_writable(ROOT.'/'.dirname(DB_NAME))){
    $erreurs[] = createError('le dossier '.dirname(DB_NAME).' n\'est pas disponible en écriture', array("Rendre le dossier inscriptible par tout le monde <kbd>sudo chmod -R 777 ".dirname(DB_NAME)."/</kbd>") );
}

if(!is_writable(ROOT.'/'.PLUGIN_DIR))
    $erreurs[] = createError('le dossier '.PLUGIN_DIR.' n\'est pas disponible en écriture', array("Rendre le dossier inscriptible par tout le monde <kbd>sudo chmod -R 777 ".PLUGIN_DIR."/</kbd>" ));

if(!Functions::checkConnectivity()){
    $notices[] = createError("votre RaspberryPi ne semble pas relié à internet, il nous es donc impossible de voir si votre version est supportée", array("Connecter votre RaspberryPi à internet"));   
}
else{
    if($supportedVersion = Functions::getSupportedVersion()){
        if(!Functions::myVersionIsSupport())
            $notices[] = createError('Votre version ne semble pas faire partie des versions supportées, plus d\'informations sur le site <a href="'.PROGRAM_WEBSITE.'">'.PROGRAM_WEBSITE.'</a> (votre version : '.$distribution.' '.$version.')');
    }
    else{
        $notices[] = createError("votre RaspberryPi n'arrive pas à communiquer avec notre site, nous ne pouvons pas voir si votre version est supportée");
    }
}




//on vérifie les envois de form
if(!empty($_['form_send_inscription'])){



    $config = new Configuration();
    $config->sgbdCreate();
    $taskList[] = "création de la base<br>";
    $config->existTable();
    $config->addConfig("test", "je suis un test");
    $taskList[] = "Remplissage de la base<br>";

    $user = new User();
    $user->sgbdCreate();
    $taskList[] = "création de la table User<br>";
    $user->existTable();
    $taskList[] = "Remplissage de la table<br>";
}
if(!empty($_['delete'])){
    if($_['token'] != $_SESSION['token'])
        $erreurs[] = "token invalide";
}

$template_infos = array(
            "title" => 'Installation - '.PROGRAM_NAME.' '.PROGRAM_VERSION,
            "js"    => ''
            );
$smarty->assign("taskList", $taskList);
$smarty->assign("erreurs", $erreurs);
$smarty->assign("notices", $notices);
$smarty->assign("template_infos", $template_infos);
$smarty->assign('executionTime',Functions::getExecutionTime($start));
$smarty->assign('debugList',Functions::getDebugList());
$smarty->display(ROOT."/vues/install.tpl");