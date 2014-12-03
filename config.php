<?php

	define('PROGRAM_NAME','EVA');
	define('PROGRAM_AUTHOR','Thibaut SEVERAC');
    define('PROGRAM_VERSION','0.3');
    define('PROGRAM_WEBSITE','http://evaproject.net'); //no end slash
	define('PROGRAM_FORUM','http://evaproject.net/forum'); //no end slash
    define('DB_TYPE','SQLITE');//SQLITE ou MYSQL
    define('DB_PREFIX','EVA_');
    define('SYSTEM_USER', 'eva');
    define('DB_NAME',ROOT.'/db/.database.db');
	define('DB_HASH','sha512');
    define('LOG_FILE','log/log.txt');
	define('PLUGIN_DIR','plugins');
	define('DEBUG', 1);

    //spécifique à smarty
    define('SMARTY_SPL_AUTOLOAD',false);
    define('SMARTY_DIR','classes/');
