<?php

/*
 @nom: RaspberryPi
 @auteur: Thib3113 (thib3113@gmail.com)
 @description:  Classe de gestion du raspberry pi
 */

class RaspberryPi extends SgdbManager{

    public $version, $revision, $pins;
    private $active_optionnal = false;
    const GPIO_DEFAULT_PATH = '/usr/local/bin/gpio';

    // version du raspberry par revision
    // http://elinux.org/RPi_HardwareHistory
    public $versionByRev = array(
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

    private $tablePins=array(
                //Physical =>    (wiringPin , nameOfPin   , Type    )
                    1  =>   array(  null    ,   "3,3V"    , "POWER" ),
                    2  =>   array(  null    ,   "5V"      , "POWER" ),
                    3  =>   array(  8       ,   "SDA.1"   , "I2C"   ),
                    4  =>   array(  null    ,   "5V"      , "POWER" ),
                    5  =>   array(  9       ,   "SCL.1"   , "I2C"   ),
                    6  =>   array(  null    ,   "0V"      , "GND"   ),
                    7  =>   array(  7       ,   "GPIO 7"  , "GPIO"  ),
                    8  =>   array(  15      ,   "TxD"     , "UART"  ),
                    9  =>   array(  null    ,   "0V"      , "GND"   ),
                    10 =>   array(  16      ,   "RxD"     , "UART"  ),
                    11 =>   array(  0       ,   "GPIO 0"  , "GPIO"  ),
                    12 =>   array(  1       ,   "GPIO 1"  , "GPIO"  ),
                    13 =>   array(  2       ,   "GPIO 2"  , "GPIO"  ),
                    14 =>   array(  null    ,   "0V"      , "GND"   ),
                    15 =>   array(  3       ,   "GPIO 3"  , "GPIO"  ),
                    16 =>   array(  4       ,   "GPIO 4"  , "GPIO"  ),
                    17 =>   array(  null    ,   "3,3V"    , "POWER" ),
                    18 =>   array(  5       ,   "GPIO 5"  , "GPIO"  ),
                    19 =>   array(  12      ,   "MOSI"    , "SPI"   ),
                    20 =>   array(  null    ,   "0V"      , "GND"   ),
                    21 =>   array(  13      ,   "MISO"    , "SPI"   ),
                    22 =>   array(  6       ,   "GPIO 6"  , "GPIO"  ),
                    23 =>   array(  14      ,   "SCLK"    , "SPI"   ),
                    24 =>   array(  10      ,   "CE0"     , "SPI"   ),
                    25 =>   array(  null    ,   "0V"      , "GND"   ),
                    26 =>   array(  11      ,   "CE1"     , "SPI"   ),
                    27 =>   array(  30      ,   "SDA.0"   , "I2C"   ),
                    28 =>   array(  31      ,   "SCL.0"   , "I2C"   ),
                    29 =>   array(  21      ,   "GPIO 21" , "GPIO"  ),
                    30 =>   array(  null    ,   "0V"      , "GND"   ),
                    31 =>   array(  22      ,   "GPIO 22" , "GPIO"  ),
                    32 =>   array(  26      ,   "GPIO 26" , "GPIO"  ),
                    33 =>   array(  23      ,   "GPIO 23" , "GPIO"  ),
                    34 =>   array(  null    ,   "0V"      , "GND"   ),
                    35 =>   array(  24      ,   "GPIO 24" , "GPIO"  ),
                    36 =>   array(  27      ,   "GPIO 27" , "GPIO"  ),
                    37 =>   array(  25      ,   "GPIO 25" , "GPIO"  ),
                    38 =>   array(  28      ,   "GPIO 28" , "GPIO"  ),
                    39 =>   array(  null    ,   "0V"      , "GND"   ),
                    40 =>   array(  29      ,   "GPIO 29" , "GPIO"  ),
    );

    //pins soudable à coté des GPIO raspberry pi B rev 2
    private $optionalPins=array(
                    1  =>   array(  null    ,   "5V"      , "POWER" ),
                    2  =>   array(  null    ,   "3,3V"    , "POWER" ),
                    3  =>   array(  17      ,   "GPIO 8"  , "GPIO"  ),
                    4  =>   array(  18      ,   "GPIO 9"  , "GPIO"  ),
                    5  =>   array(  19      ,   "GPIO 10" , "GPIO"  ),
                    6  =>   array(  20      ,   "GPIO 11" , "GPIO"  ),
                    7  =>   array(  null    ,   "0V"      , "GND"   ),
                    8  =>   array(  null    ,   "0V"      , "GND"   ),
     );

    //todo : edit pins, i can juste make this for b+1.0
    public $pinsByVer = array(
                          "beta"  => array( 28  ,   0   ),
                          "A1.0"  => array( 26  ,   0   ),
                          "A2.0"  => array( 26  ,   0   ),
                          "B1.0"  => array( 26  ,   0   ),
                          "B2.0"  => array( 26  ,   8   ),
                          "B+1.0" => array( 40  ,   0   )

    );


    function __construct(){
      $this->setVersion();
      $this->checkPins();
      $this->nameTable();
    }

    private function nameTable(){
      foreach ($this->tablePins as $key => $value) {
        $this->tablePins[$key]["wiringPin"] = $value[0];
        $this->tablePins[$key]["nameOfPin"] = $value[1];
        $this->tablePins[$key]["type"] = $value[2];
      }

      foreach ($this->optionalPins as $key => $value) {
        $this->optionalPins[$key]["wiringPin"] = $value[0];
        $this->optionalPins[$key]["nameOfPin"] = $value[1];
        $this->optionalPins[$key]["type"] = $value[2];
      }
    }

    public function setVersion(){
      if(empty($this->version))
        $this->version = $this->getRaspVersion();
    }

    private function exec($cmd, $system_user = true){
        global $system;
        // echo $cmd;
        return $system->shell($cmd, $system_user); 
    }

    public function mode($pin,$mode = 'out'){
        return $this->exec(self::GPIO_DEFAULT_PATH.' mode '.$pin.' '.$mode);
    }
    public function write($pin,$value = 0,$automode = false){
        if($automode) $this->mode($pin,'out');
        return $this->exec(self::GPIO_DEFAULT_PATH.' write '.$pin.' '.$value);
    }
    public function read($pin,$automode = false){
        if($automode) $this->mode($pin,'in');
        return $this->exec(self::GPIO_DEFAULT_PATH.' read '.$pin);
    }

    public function toggle($pin,$automode = false){
        if($automode) $this->mode($pin,'out');
        return $this->exec(self::GPIO_DEFAULT_PATH.' toggle '.$pin);
    }


    public function checkPins(){
      //on récupère les pins
      $this->pins = $this->tablePins;
      if($this->active_optionnal){
        foreach ($this->optionalPins as $key => $value) {
          $this->pins[] = $value;
        }
      }

      //on récupère les informations du readall
      $read_all = $this->exec("gpio readall", true);
      foreach ($read_all as $key => $value) {
        //on match les lignes dans les bonnes cases
        preg_match("~\|(?:(?'LBCM'\s*[0-9]*\s*)\|(?'LwPi'\s*[0-9]*\s*)\|(?'LName'\s*[^\|]*\s*)\|(?'LMode'\s*[^\|]*\s*)\|(?'LValue'\s*[^\|]*\s*)\|(?'LPhysical'\s*[0-9]*\s*))\|\|(?:(?'Rphysical'\s*[0-9]*\s*)\|(?'RValue'\s*[0-9]*\s*)\|(?'RMode'\s*[^\|]*\s*)\|(?'RName'\s*[^\|]*\s*)\|(?'RwPi'\s*[^\|]*\s*)\|(?'RBCM'\s*[0-9]*\s*))\s*\|~i", $value, $matches);
        
        //on les met dans le tableau ( gauche du tableau, puis droite)
        if(!empty($matches["LPhysical"]))
          $this->pins[trim($matches["LPhysical"])]["value"] = trim($matches["LValue"]);

        if(!empty($matches["Rphysical"]))
          $this->pins[trim($matches["Rphysical"])]["value"] = trim($matches["RValue"]);
        
      }
    }

    public function readAll(){
        $this->checkPins();
        return $this->pins;
    }


    /**
     * envoi state sur le pin
     * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
     * @param int $pin
     * @param binary $state
     * @return false en cas de problème, valeur du pin dans le cas où c'est bon
     */
    public function changePinState($pin){
        if(!is_numeric($pin)){
            return false;
        }

        //on change la valeur du pin
        $this->toggle($pin, true);
        //on retourne la valeur du pin
        return $this->read($pin);
    }

    /**
     * renvois la table Wiring pi ( gpio version: 2.20 )
     * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
     * @return array contenant la table table
     */
    public function getTablePins(){
        return $this->tablePins;
    }

    /**
     * renvoi le pin du WiringPin
     * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
     * @return number : numero du pin
     */
    public function getPinsFromWiringPins($wiringPins){
        //on passe tous les pins
        foreach ($this->tablePins as $key => $infos) {
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
    public function getPinsWiringFromPins($pins){
        return $this->tablePins[$pins][0];
    }

    /**
     * renvoi le nom du pin
     * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
     * @return number : numero du Wiringpin
     */
    public function getNameOfWiringPins($wiringPins){
        //on passe tous les pins
        foreach ($this->tablePins as $infos) {
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
    public function getNameOfPins($pin){
        return $this->tablePins[$pin][1];
    }

    public function getListPins(){
        $tempPinsTable = array();
        $this->setVersion();

        //on prend le nombre de pins correspondant à la version utilisée
        for ($i=1; $i <= $this->getNumberOfPins($this->version); $i++) {
            $tempPinsTable[] = $this->tablePins[$i];
        }
        return $tempPinsTable;
    }

    public function getListOptionalPins(){
        $tempPinsTable = array();
        $this->setVersion();

        //on prend le nombre de pins correspondant à la version utilisée
        for ($i=1; $i <= $this->getNumberOfOptionalPins($this->version); $i++) {
            $tempPinsTable[] = $this->optionalPins[$i];
        }
        return $tempPinsTable;
    }

    public function getAllState(){
        $pins = $this->readAll();
        $return = array();
        foreach ($pins as $key => $value) {
            if(isset($value['value']) && !is_null($value['value']))
                $return[] = array("id" => $key, "state" => $value["value"]);
        }
        return $return;
    }

  /**
   * Permet de récupéré la version du raspberry
   * @author Thibaut SEVERAC ( thibaut@thib3113.fr )
   * @return la version du raspberryPi
  */
  public function getRaspVersion(){
      //retourne la revision du raspberry
      $revision = exec("cat /proc/cpuinfo | grep Revision | rev  | cut -c1-4 | rev"); // on lis les infos du proc, on cherche la revision, on retourne la chaine ( les versions renvois 00XX, les overclocké renvois 100XX), on récupère les 4 derniers, on retourne la chaine
      $this->setRevision($revision);
      $version = !empty($this->versionByRev[$revision])? $this->versionByRev[$revision] : false;
      return $version;
  }


    /**
     * Connaitre la liste des wiring pin
     * @param  Version $version Version du raspberry pi donnee par la fonction adequate
     * @return array          liste des wiring pin
     */
    public function getListWiringPin(){
        $list = array();
        $tempPinsTable = array();

        //on prend le nombre de pins correspondant à la version utilisée
        for ($i=1; $i <= $this->getNumberOfPins(); $i++) { 
            $tempPinsTable[] = $this->tablePins[$i];
        }
        foreach ($tempPinsTable as $value) {
            if(!is_null($value[0]))
                $list[$value[0]] = $value[1];
        }
        ksort($list, SORT_NUMERIC);
        return $list;
    }

    public function getListOptionalWiringPin(){
        $list = array();
        $tempPinsTable = array();

        //on ajoute les pins optionnels
        for ($i=1; $i <= $this->getNumberOfOptionalPins(); $i++) {
            $tempPinsTable[] = $this->optionalPins[$i];
        }

        foreach ($tempPinsTable as $value) {
            if(!is_null($value[0]))
                $list[$value[0]] = $value[1];
        }

        ksort($list, SORT_NUMERIC);
        return $list;
    }

    public function getNumberOfPins(){
        $this->setVersion();
        return $this->pinsByVer[$this->version][0];
    }

    public function getNumberOfOptionalPins(){
        $this->setVersion();
        return $this->pinsByVer[$this->version][1];
    }

    public function getInfos($key, $details = false){
      switch (strtolower($key)) {
        case 'distribution':
            $distribution = $this->exec("cat /etc/*-release | grep PRETTY_NAME= | cut -c14- | rev | cut -c2- | rev");
            if(!$details){
              if(preg_match("~Raspbian~Uis", $distribution))
                return "Raspbian";
            }
            return $distribution;

          break;
        case 'version':
          return $this->exec("uname -r");
        break;
        case 'wiringpi':
          return $this->exec(self::GPIO_DEFAULT_PATH.' -v | grep "gpio version" | cut -c15-');
          break;
        case 'git':
          return $this->exec("git --version");
          break;
        case 'revision':
          return $this->revision;
        break;
        default:
            return false;
          break;
      }
    }

    public function setRevision($revision){
      $this->revision = $revision;
    }
}
?>