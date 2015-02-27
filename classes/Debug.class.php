<?php
/*
 @nom: RaspberryPi
 @auteur: Thib3113 (thib3113@gmail.com)
 @description:  Classe de debugguage
 */

class Debug extends SgdbManager{
    private $debugActiveItem = array('ALL' => false);
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

    public function addCustomQuery($query){
        $backtrace = $this->whoCallMe(1);
        $return = self::_query($query, null, $backtrace['file'], $backtrace['line']);

        if($return){
            $return = $return->fetch();
            if(is_array($return)){
                $result = '<ul>';
                foreach ($return as $key => $value) {
                    $result .= '<li>'.$key.' = <kbd>'.$value.'</kdb></li>';
                }
                $result .= '</ul>';
                $return = $result;
            }
        }
        else
            $return = "EMPTY";

        $this->addDebugList(array("custom" => $return));
    }

    public function whoCallMe($number = 1){
        $backtrace = debug_backtrace();

        return $backtrace[$number];
    }

    private function echoArray($array, $depth = 0, $maxDepth = 3){
        $return ="";
        if(!is_array($array) && !is_object($array))
            $return .= "$array";
        elseif(is_object($array)){
            $return .= "objet";
        }
        else{
            foreach ($array as $key => $value) {
                if(!is_array($value))
                    $return .="<li>$key - <kbd>$value</kbd></li>\n";
                else{
                    $return .="<li>$key - <ul>";
                    $return .= $this->echoArray($value, $depth+1, $maxDepth);
                    $return .="</lu></li>";
                }
            }
        }
        return $return;
    }

    public function addBasicDebug(){
        foreach ($_COOKIE as $key => $value) {
            if(is_array($value) || Functions::isSerialized($value)){
                if(Functions::isSerialized($value))
                    $array_value = unserialize($value);
                else
                    $array_value = $value;

                $value = '<ul>';
                $value .= $this->echoArray($array_value);
                $value .= '</ul>';
            }
            else
                $value = "<kbd>$value</kbd>";


            $return = '<span class="label label-primary">'.$key.'</span> = '.$value.''; 
            $this->addDebugList(array("cookie" => $return), false);
        }

        foreach ($_SESSION as $key => $value) {
            if(is_array($value) || Functions::isSerialized($value)){
                if(Functions::isSerialized($value))
                    $array_value = unserialize($value);
                else
                    $array_value = $value;

                $value = '<ul>';
                $value .= $this->echoArray($array_value);
                $value .= '</ul>';
            }
            else
                $value = "<kbd>$value</kbd>";



            $return = '<span class="label label-primary">'.$key.'</span> = '.$value.''; 
            $this->addDebugList(array("session" => $return), false);
        }
    }

    public function getDebugList(){
        if(DEBUG && !Functions::isAjax()){
            //on écris les debug
            $listDebug = array();
            foreach ($this->debugItems as $key => $valeurs) {
                if($this->debugActiveItem['ALL'] || $this->debugActiveItem[strtoupper($key)])
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

//     private function varDumpChild($expression){

//     }

//     public function var_dump($var, $key = NULL, $maxDepth = 3, $depth = 0, $parent_number = 0){
//         $opt = array(
//                         "serialize" => false,
//                         "base64"    => false
//                         );
//         $return = "";

//         if($depth == 0)
//             $return .= '<pre><div class="var_dump">';

//         $var_ = $var;
//         //si var est un tableau serializé
//         if(@unserialize($var)){
//             $opt['serialize'] = true;
//             $varTemp = unserialize($var);
//             $var_ = $var;
//             $var = $varTemp;
//         }

//         //si var est encoder en base64
//         if(!is_object($var) && !is_array($var) && Functions::isBase64Encoded($var)){
//             $opt['base64'] = true;
//             $varTemp = base64_decode($var);
//             $var_ = $var;
//             $var = $varTemp;
//         }

//         $return .= "<ul>";

//         $typeof = gettype($var);
//         switch ($typeof) {
//             case 'boolean':
//             case 'NULL':
//                 $color = "#8B1796";
//                 $var_ = ($var_)? "<b>TRUE</b>" :($var_ === NULL)? "<b>NULL</b>" : "<b>FALSE</b>";
//             break;
//             case 'integer':
//             case 'double':
//                 $color = "#FFA000";
//                 $var = $var;
//             break;    
//             case 'string':
//                 $color = "#FF0000";
//                 if($var[0] == "`")
//                     $var = '"'.$var.'"';
//                 else
//                     $var = "`$var`";
//             break;
//             case 'object':
//                 $color = '#1992D3';

//             break;
//             default:
//                 $color = "#E85DB7";
//             break;
//         }
//         $return .= '<li>'.($key === NULL? "" : "$key : ").'<span class="label label-danger">('.$typeof.')</span>';

//         $return .= "<!-- is_array : ".(is_array($var)? "TRUE" : "FALSE")." -->";
//         // <span style="color:'.$color.'">'.$var_.'</span>'.($opt['base64']? ' => '.$var : '').' </li>
//         if(is_array($var) || is_object($var)){
//             if(is_object($var)){
//                 $class = new ReflectionClass( $var ); 
//                 $name = $class->getName();
//                 $return .= " \"$name\"";
//             }

//             $numberArray = 0;
//             foreach ($var as $key => $var) {
//                 if($depth<$maxDepth){
//                     $return .= "\n<!-- depth : $depth -->\n";
//                     $return .= $this->var_dump($var, $key, $maxDepth, $depth+1, $numberArray);
//                 }
//                 else{
//                     try {
//                         $count = count($var, 1);
//                         $return = "<ul><li>$key <span class=\"label label-danger\">(array)</span> ".($count>0? "($count element".($count>1? "s": "")." more) ..." : "(empty)")."</li></ul>";
//                     } catch (Exception $e) {
//                         $return .= "<ul><li>error found : $e</li></ul>";
//                     }
//                 }
//             }
//         }
//         else
//             $return .= ' <span style="color:'.$color.'">'.$var_.'</span>'.($opt['base64']? ' <span class="label label-info">base64 =></span> '.$var : '');

//         $return .= "</li></ul>";

//         if($depth == 0)
//             $return .= '</div></pre>';

//         // if(is_array($var) || is_object($var)){
//         //     $return .= '<li>'.($opt['serialize']? '<span class="label label-info">serialize</span> ' : '').''.($opt['serialize']? $var_.' => ' : '').'<span class="label label-danger">'.gettype($var).'</span> ('.count($var).' element'.(count($var)>1? "s" : "").')';
//         //     foreach ($var as $key => $var) {
//         //         if($depth<$maxDepth)
//         //             $return .= $this->var_dump($var, $maxDepth, $depth+1, $numberArray++);
//         //         else{
//         //             try {
//         //                 $count = count($var, 1);
//         //                 $return = "<ul><li>... ( $count element".($count>1? "s": "")." more )</li></ul>";
//         //             } catch (Exception $e) {
//         //                 $return .= "<ul><li>error found : $e</li></ul>";
//         //             }
//         //         }

//         //     }
//         // }
            
//         //     $return .= "</li>";

// /*
//                         else{
                
//                 $class = new ReflectionClass( $var ); 
//                 $name = $class->getName(); 
//                 $return .= '<li><span class="label label-warning">object</span> <span style="color:'.$color.'">'.$name.'</span>';
//                 $i = 0;
//                 foreach ($var as $key => $value) {
//                     if($i == 0)
//                         $return .= ": <ul>";

//                     if($depth<$maxDepth)
//                         $return .= $this->var_dump($var, $maxDepth, $depth+1, $numberArray++);
//                     else{
//                         try {
//                             $count = count($var, 1);
//                             $return = "<ul><li>... ( $count element".($count>1? "s": "")." more )</li></ul>";
//                         } catch (Exception $e) {
//                             $return .= "<ul><li>error found : $e</li></ul>";
//                         }
//                     }

//                     $return .= '<li>'.$key.' => ';

//                     $return .= $this->var_dump( $value, $maxDepth, $depth+1, $numberArray);
                    

//                     $return .= '</li>';
//                     $i++;
//                 }
//                 if($i>0)
//                     $return .= '</ul>';
//                 $return .= '</li>';*/
//         // }
//         // else{


//         return $return;

//     }

}
?>