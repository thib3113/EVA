<?php
    require __DIR__.'/static.php';

    define('DB_TYPE','SQLITE');
    define('DB_PREFIX','EVA_');
    define('SYSTEM_USER', 'eva');
    define('DB_NAME', ROOT.'/db/.database.db');
    define('DB_HASH','sha512');
    define('LOG_FILE', ROOT.'/log/log.txt');
    define('DEBUG', 1);

