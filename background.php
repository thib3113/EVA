<?php
$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli'){
    header('HTTP/1.0 403 Forbidden');
    exit("403 - Forbidden");
}

define('ROOT', __DIR__);
require ROOT.'/base.php';

while(true){
    $numberOfPins = count(RaspberryPi::getListWiringPin()) + count(RaspberryPi::getListOptionalWiringPin());
    for ($i=0; $i < $numberOfPins; $i++) {
        echo "pin $i : ".RaspberryPi::read($i)."\n";
    }
    usleep(10);
}
?>