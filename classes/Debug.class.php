<?php
/*
 @nom: RaspberryPi
 @auteur: Thib3113 (thib3113@gmail.com)
 @description:  Classe de debugguage
 */

class Debug{
    private $debugActiveItem = array( 
                                'COOKIE'   => false ,
                                'SESSION'   => false ,
                                'CONNEXION' => false ,
                                'SQL'       => false
                                         );
    private $debugItems = array();
    private $debugId = 0;

    public function __construct($items = array("ALL") ){
        foreach ($items as $item) {
            $item = strtoupper($item);
            if( $item == 'ALL'){
                foreach ($this->debugActiveItem as $key => $value) {
                    $this->debugActiveItem[$key] = true;
                }    
            }
            else{
                if($this->debugActiveItem[$item] === false)
                    $this->debugActiveItem[$item] = true;
            }
        }
    }

    public function addBasicDebug(){
        foreach ($_COOKIE as $key => $value) {
            if(@unserialize($value)){
                $array_value = unserialize($value);
                $value = '<ul>';
                foreach ($array_value as $clef => $valeur) {
                    $value .= "<li>$clef - <kbd>$valeur</kbd></li>";
                }
                $value .= '</ul>';
            }
            else
                $value = "<kbd>$value</kbd>";


            $return = '<span class="label label-primary">'.$key.'</span> = '.$value.''; 
            $this->addDebugList(array("cookie" => $return), false);
        }

        foreach ($_SESSION as $key => $value) {
            if(@unserialize($value)){
                $array_value = unserialize($value);
                $value = '<ul>';
                foreach ($array_value as $clef => $valeur) {
                    $value .= "<li>$clef - <kbd>$valeur</kbd></li>";
                }
                $value .= '</ul>';
            }
            else
                $value = "<kbd>$value</kbd>";


            $return = '<span class="label label-primary">'.$key.'</span> = '.$value.''; 
            $this->addDebugList(array("session" => $return), false);
        }
    }

    public function getDebugList(){
    global $smarty;
        if(DEBUG && !Functions::isAjax()){
            //on écris les debug
            $listDebug = array();
            foreach ($this->debugItems as $key => $valeurs) {
                if($this->debugActiveItem[strtoupper($key)])
                    $listDebug[$key] = $valeurs;   
            }
            return $listDebug;
        }
    }

    public function debugId(){
        return ++$this->debugId;
    }

    public function addDebugList($debugItems, $time = NULL){
        $time = (($time === NULL)? Functions::getExecutionTime(true) :((!$time)? false : $time));
        // var_dump($$this->debugItems);
        foreach ($debugItems as $key => $value) {
             $this->debugItems[$key][] = array(
                "id"    => $this->debugId(),
                "time"  => $time ,
                "value" => $value
                );
         } 
    }

    public function var_dump($var, $maxDepth = 3, $depth = 0, $numberArray = 0){
        $opt = array(
                        "array"     => true,
                        "serialize" => false,
                        "base64"    => false
                        );
        $return = "";

        if($depth == 0)
            $return .= '<pre><div class="var_dump">';

        $var_ = $var;
        //si var est un tableau serializé
        if(@unserialize($var)){
            $opt['serialize'] = true;
            $varTemp = unserialize($var);
            $var_ = $var;
            $var = $varTemp;
        }

        //si var est encoder en base64
        if(!is_object($var) && !is_array($var) && Functions::isBase64Encoded($var)){
            $opt['base64'] = true;
            $varTemp = base64_decode($var);
            $var_ = $var;
            $var = $varTemp;
        }

        $return .= "<ul>";

        if(is_array($var)){
            $return .= '<li>'.($opt['serialize']? '<span class="label label-info">serialize</span> ' : '').''.($opt['serialize']? $var_.' => ' : '').'<span class="label label-danger">Array</span> ( '.count($var).' element'.(count($var)>1? "s" : "").')';
            foreach ($var as $key => $var) {
                if($depth<$maxDepth)
                    $return .= $this->var_dump($var, $maxDepth, $depth+1, $numberArray++);
                else{
                    try {
                        $count = count($var, 1);
                        $return = "<ul><li>... ( $count element".($count>1? "s": "")." more )</li></ul>";
                    } catch (Exception $e) {
                        $return .= "<ul><li>error found : $e</li></ul>";
                    }
                }

            }
            
            $return .= "</li>";
        }
        else{
            $typeof = gettype($var);
            switch ($typeof) {
                case 'boolean':
                    $color = "#8B1796";
                    $var = ($var)? "<b>true</b>" : "<b>false</b>";
                break;
                case 'integer':
                case 'double':
                    $color = "#FFA000";
                    $var = $var;
                break;    
                case 'string':
                    $color = "#FF0000";
                    if($var[0] == "`")
                        $var = '"'.$var.'"';
                    else
                        $var = "`$var`";
                break;
                case 'object':
                    $color = '#1992D3';
                break;
                default:
                    $color = "#E85DB7";
                break;
            }
                if($typeof != "object")
                    $return .= '<li>'.($depth>0?'<span class="label label-default">'.$numberArray.'</span> : ' : "").' '.($opt['base64']? '<span class="label label-info">base64</span> ' : '').'<span class="label label-danger">('.$typeof.')</span> <span style="color:'.$color.'">'.$var_.'</span>'.($opt['base64']? ' => '.$var : '').' </li>';
                else{
                    
                    $class = new ReflectionClass( $var ); 
                    $name = $class->getName(); 
                    $return .= '<li><span class="label label-warning">object</span> <span style="color:'.$color.'">'.$name.'</span></li>';
                }
        }
        $return .= "</ul>";

        if($depth == 0)
            $return .= '</div></pre>';

        return $return;

    }

}
?>