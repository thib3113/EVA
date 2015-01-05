<?php

CLass SevenSegment{
    private $type;
    private $pins;

    function __construct($pins = array(), $type = "standalone"){
        $this->type = $type;
    }

    function affich($texte){
        global $system;
        if($this->type == "standalone"){
            var_dump($this->type);
            $system->shell("python ".__DIR__."/app/sevensegment_affich.py \"$texte\" ", true);
        }
        else{
            if(is_file(__DIR__."/app/".$this->type."/sevensegment_affich.py")){
                // var_dump("python ".__DIR__."/app/".$this->type."/sevensegment_affich.py \"$texte\" ");
                $system->shell("python ".__DIR__."/app/".$this->type."/sevensegment_affich.py \"$texte\" ", true);
            }
            else{
                var_dump(__DIR__."/app/".$this->type."/sevensegment_affich.py");
                return false;
            }
        }
    }

}