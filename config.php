<?php

	define('PROGRAM_NAME','EVA');
	define('PROGRAM_AUTHOR','Thibaut SEVERAC');
    define('PROGRAM_VERSION','0.2');
	define('PROGRAM_WEBSITE','http://evaproject.net'); //no end slash
    define('DB_TYPE','SQLITE');//SQLITE ou MYSQL
    define('DB_PREFIX','EVA_');
	define('DB_NAME','db/.database.db');
    define('LOG_FILE','log/log.txt');
	define('PLUGIN_DIR','plugins');
	define('DEBUG', 1);
    define('SMARTY_SPL_AUTOLOAD',false);
    define('SMARTY_DIR','classes/');