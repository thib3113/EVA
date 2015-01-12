<?php

Class LED{
    private $nb_pins = 2;
    private $pins = array();
    /**
     * Création d'une Led
     * @param Array $pins :
     *        @param  int wiringPin 
     *        @param  bool power
     */
    function __construct($pins){
        $this->setNumberOfPins(count($pins)> 2? count($pins) : 2);
        foreach ($pins as $key => $value) {
            $pins[$key][1] = (!isset($value[1])? true : $value[1]);
        }
        $this->setPins($pins);
    }

    public function setNumberOfPins($number_of_pins){
        $this->nb_pins = $number_of_pins;
    }

    public function setPins($pins){
        $this->pins = $pins;
    }

    /**
     * Allume/éteint la led
     * @param  void $state état de la led ( on/off, true/false, 0/1)
     */
    public function power($state){
        global $RaspberryPi;

        $state = ($state == "on")? true : $state;
        $state = ($state == "off")? false : $state;

        foreach ($this->pins as $pin) {
            if($state){
                $RaspberryPi->write(abs($pin[0]), (($pin[1])? 1 : 0), true );
            }
            else{
                $RaspberryPi->write(abs($pin[0]), (($pin[1])? 0 : 1), true );
            }


        }

    }
}