<?php

/*
 @nom: Gpio
 @auteur: Thib3113 (thib3113@gmail.com)
 @description:  Classe de gestion du raspberry pi
 */

class RaspberryPi extends SgdbManager{

    const GPIO_DEFAULT_PATH = '/usr/local/bin/gpio';

    // version du raspberry par revision
    // http://elinux.org/RPi_HardwareHistory
    public static $versionByRev = array(
                          "beta" => "beta",
                          "0002" => "B1.0",
                          "0003" => "B1.0",
                          "0004" => "B2.0",
                          "0005" => "B2.0",
                          "0006" => "B2.0",
                          "0007" => "A2.0",
                          "0008" => "A2.0",
                          "0009" => "A2.0",
                          "000d" => "B2.0",
                          "000e" => "B2.0",
                          "000f" => "B2.0",
                          "0010" => "B+1.0",
    );

    private static $TablePins=array(
                    //Physical =>  (wiringPin, nameOfPin)
                    1  =>   array(  null    ,   "3,3V"      ),
                    2  =>   array(  null    ,   "5V"        ),
                    3  =>   array(  8       ,   "SDA.1"     ),
                    4  =>   array(  null    ,   "5V"        ),
                    5  =>   array(  9       ,   "SCL.1"     ),
                    6  =>   array(  null    ,   "0V"        ),
                    7  =>   array(  7       ,   "GPIO 7"    ),
                    8  =>   array(  15      ,   "TxD"       ),
                    9  =>   array(  null    ,   "0V"        ),
                    10 =>   array(  16      ,   "RxD"       ),
                    11 =>   array(  0       ,   "GPIO 0"    ),
                    12 =>   array(  1       ,   "GPIO 1"    ),
                    13 =>   array(  2       ,   "GPIO 2"    ),
                    14 =>   array(  null    ,   "0V"        ),
                    15 =>   array(  3       ,   "GPIO 3"    ),
                    16 =>   array(  4       ,   "GPIO 4"    ),
                    17 =>   array(  null    ,   "3,3V"      ),
                    18 =>   array(  5       ,   "GPIO 5"    ),
                    19 =>   array(  12      ,   "MOSI"      ),
                    20 =>   array(  null    ,   "0V"        ),
                    21 =>   array(  13      ,   "MISO"      ),
                    22 =>   array(  6       ,   "GPIO 6"    ),
                    23 =>   array(  14      ,   "SCLK"      ),
                    24 =>   array(  10      ,   "CE0"       ),
                    25 =>   array(  null    ,   "0V"        ),
                    26 =>   array(  11      ,   "CE1"       ),
                    27 =>   array(  30      ,   "SDA.0"     ),
                    28  =>  array(  31      ,   "SCL.0"     ),
                    29  =>  array(  21      ,   "GPIO21"    ),
                    30  =>  array(  null    ,   "0V"        ),
                    31  =>  array(  22      ,   "GPIO22"    ),
                    32  =>  array(  26      ,   "GPIO26"    ),
                    33  =>  array(  23      ,   "GPIO23"    ),
                    34  =>  array(  null    ,   "0V"        ),
                    35  =>  array(  24      ,   "GPIO24"    ),
                    36  =>  array(  27      ,   "GPIO27"    ),
                    37  =>  array(  25      ,   "GPIO25"    ),
                    38  =>  array(  28      ,   "GPIO28"    ),
                    39  =>  array(  null    ,   "0V"        ),
                    40  =>  array(  29      ,   "GPIO29"    ),
    );

    //pins soudable à coté des GPIO raspberry pi B rev 2
    private static $OptionalPins=array(
                    1    =>  array(  null    ,   "5V"        ),
                    2    =>  array(  null    ,   "3,3V"      ),
                    3    =>  array(  17      ,   "GPIO 8"    ),
                    4    =>  array(  18      ,   "GPIO 9"    ),
                    5    =>  array(  19      ,   "GPIO10"    ),
                    6    =>  array(  20      ,   "GPIO11"    ),
                    7    =>  array(  null    ,   "0V"        ),
                    8    =>  array(  null    ,   "0V"        ),
     );

    //todo : edit pins, i can juste make this for b+1.0
    public static $pinsByVer = array(
                          "beta"  => array( 28  ,   0   ),
                          "A1.0"  => array( 26  ,   0   ),
                          "A2.0"  => array( 26  ,   0   ),
                          "B1.0"  => array( 26  ,   0   ),
                          "B2.0"  => array( 26  ,   8   ),
                          "B+1.0" => array( 40  ,   0   )

    );

    private static function exec($cmd){
        // var_dump($cmd);
        return exec($cmd);
    }

    public static function mode($pin,$mode = 'out'){
        return self::exec(self::GPIO_DEFAULT_PATH.' mode '.$pin.' '.$mode);
    }
    public static function write($pin,$value = 0,$automode = false){
        if($automode) self::mode_($pin,'out');
        return self::exec(self::GPIO_DEFAULT_PATH.' write '.$pin.' '.$value);
    }
    public static function read($pin,$automode = false){
        if($automode) self::mode_($pin,'in');
        return self::exec(self::GPIO_DEFAULT_PATH.' read '.$pin);
    }

    public static function toggle($pin,$automode = false){
        if($automode) self::mode_($pin,'out');
        return self::exec(self::GPIO_DEFAULT_PATH.' toggle '.$pin);
    }


    /**
     * envoi state sur le pin
     * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
     * @param int $pin
     * @param binary $state
     * @return false en cas de problème, valeur du pin dans le cas où c'est bon
     */
    public static function changePinState($pin){
        if(!is_numeric($pin)){
            return false;
        }

        //on change la valeur du pin
        self::toggle_($pin, true);
        //on retourne la valeur du pin
        return self::read_($pin);
    }

    /**
     * renvois la table Wiring pi ( gpio version: 2.20 )
     * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
     * @return array contenant la table table
     */
    public static function getTablePins(){
        return self::$TablePins;
    }

    /**
     * renvoi le pin du WiringPin
     * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
     * @return number : numero du pin
     */
    public static function getPinsFromWiringPins($wiringPins){
        //on passe tous les pins
        foreach (self::$TablePins as $key => $infos) {
            //on passe toutes les valeurs
            foreach ($infos as $value) {
                if($value[0] == $wiringPins)
                    return $key;
            }
        }
        return false;
    }

    /**
     * renvoi le WiringPin du pin
     * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
     * @return number : numero du pin
    */
    public static function getPinsWiringFromPins($pins){
        return self::$TablePins[$pins][0];
    }

    /**
     * renvoi le nom du pin
     * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
     * @return number : numero du Wiringpin
     */
    public static function getNameOfWiringPins($wiringPins){
        //on passe tous les pins
        foreach (self::$TablePins as $infos) {
            if($infos[0] === $wiringPins){
                    return $infos[1];
            }
        }
        return false;
    }

    /**
     * renvoi le nom du pin
     * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
     * @return number : numero du pin
     */
    public static function getNameOfPins($pin){
        return self::$TablePins[$pin][1];
    }

    public static function getListPins($version){
        $tempPinsTable = array();

        //on prend le nombre de pins correspondant à la version utilisée
        for ($i=1; $i <= self::getNumberOfPins($version); $i++) { 
            $tempPinsTable[] = self::$TablePins[$i];
        }
        return $tempPinsTable;
    }
    
    public static function getListOptionalPins($version){
        $tempPinsTable = array();

        //on prend le nombre de pins correspondant à la version utilisée
        for ($i=1; $i <= self::getNumberOfOptionalPins($version); $i++) { 
            $tempPinsTable[] = self::$TablePins[$i];
        }
        return $tempPinsTable;
    }


    /**
     * Connaitre la liste des wiring pin
     * @param  Version $version Version du raspberry pi donnee par la fonction adequate
     * @return array          liste des wiring pin
     */
    public static function getListWiringPin($version){
        $list = array();
        $tempPinsTable = array();

        //on prend le nombre de pins correspondant à la version utilisée
        for ($i=0; $i <= self::getNumberOfPins($version); $i++) { 
            $tempPinsTable[] = self::$TablePins[$i];
        }
        foreach ($tempPinsTable as $value) {
            if(!is_null($value[0]))
                $list[$value[0]] = $value[1];
        }
        ksort($list, SORT_NUMERIC);
        return $list;
    }

    public static function getListOptionalWiringPin($version){
        $list = array();
        $tempPinsTable = array();

        //on ajoute les pins optionnels
        for ($i=0; $i <= self::getNumberOfOptionalPins($version); $i++) { 
            $tempPinsTable[] = self::$TablePins[$i];
        }

        foreach ($tempPinsTable as $value) {
            if(!is_null($value[0]))
                $list[$value[0]] = $value[1];
        }

        ksort($list, SORT_NUMERIC);
        return $list;
    }

    public static function getNumberOfPins($version){
        return self::$pinsByVer[$version][0];
    }

    public static function getNumberOfOptionalPins($version){
        return self::$pinsByVer[$version][1];
    }

    /**
     * Permet de récupéré la version du raspberry
     * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
     * @param int $pin
     * @param binary $state
     * @return false en cas de problème, valeur du pin dans le cas où c'est bon
    */
    public static function getRaspVersion(){
        //retourne la revision du raspberry
        $revision = exec("cat /proc/cpuinfo | grep Revision | rev  | cut -c1-4 | rev"); // on lis les infos du proc, on cherche la revision, on retourne la chaine ( les versions renvois 00XX, les overclocké renvois 100XX), on récupère les 4 derniers, on retourne la chaine
        $version = !empty(self::$versionByRev[$revision])? self::$versionByRev[$revision] : false;
        return $version;
    }
}
?>