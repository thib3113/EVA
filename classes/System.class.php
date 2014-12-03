<?php

class System {
    private $user = ""; //nom de l'utilisateur système
    private $distrib = ""; //nom de la distribution utilisée
    private $distribVersion = ""; //numéro de version de la distribution utilisée
    private $last_status= 0;
    private $specialized_list;
    private $specialized_file;

    public function __construct(){
        //charge les spécialité de l'os en cours
        $this->specialized_file = ROOT.DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."specialized".DIRECTORY_SEPARATOR.strtolower($this->getCurrentDistrib()).".php";
        if(is_file($this->specialized_file)){
            require $this->specialized_file;
            $this->specialized_list = $specialized_list;
        }
        else{
            Functions::fatal_error("Le fichier des spécialités de votre OS est manquant !<br> Il devrais se trouver à cet endroit <kbd>$specialized_file</kbd>");
        }

        //on cherche l'utilisateur courant
        $this->setUser($this->getProcessUser());

        //on cherche la distrib courante
        $this->setDistrib($this->getCurrentDistrib());

        //on regarde si l'utilisateur systeme est installé
    }

    public function getProcessUser(){
        $processUser = posix_getpwuid(posix_geteuid());
        return $processUser['name'];

    }

    public function shell($str, $system_user = false){
        exec(($system_user? "sudo -u ".SYSTEM_USER." " : "").$str, $return, $status);
        if(count($return) < 2 && $status == 0)
            $return = $return[0];
        else
            $this->last_status = $status;
        return $return;
    }

    public function getCurrentDistrib(){
        //Pour rajouter une distrib, il faut l'ajouter dans la liste, puis l'ajouter dans le switch en dessous
        $getCurrentDistribByVersion = array(
            "Raspbian",

            );

        foreach ($getCurrentDistribByVersion as $value) {

            switch ($value) {
                case 'Raspbian':
                    $return = $this->shell("cat /etc/*-release | grep PRETTY_NAME= | cut -c14- | rev | cut -c2- | rev");
                    if(preg_match("~Raspbian~Uis", $return))
                        return 'Raspbian';
                break;

                default:
                    return $return;
                break;
            }
        }
        $return = $this->shell();
    }

    public function getCurrentDistribVersion(){
        //on récupère la version de la distrib
        $return = $this->shell($this->getSpecialized("getCurrentDistribVersion"));
        return $return;
    }

    //setter
    public function setUser($user){
        $this->user = $user;
    }
    public function setDistrib($distrib){
        $this->distrib = $distrib;
    }

    //getter
    public function getUser(){
        return $this->user;
    }
    public function getDistrib(){
        return $this->distrib;
    }

    public function getSpecialized($name){
        if(!empty($this->specialized_list[$name]))
            return $this->specialized_list[$name];
        else
            Functions::fatal_error("la fonction $name n'est pas présente dans votre fichier de spécialités <kbd>".$this->specialized_file."</kbd>");
    }

    function getNetworkInfos(){
            $liste_interface = $this->shell($this->getSpecialized("getListNetworkInterfaces"), true);

            foreach ($liste_interface as $value) {
                $interface[$value] = $this->shell("ifconfig $value", true);
            }
            foreach ($interface as $key => $values) {
                $current_interface = array();
                $current_interface['active'] = false;
                $current_interface['broadcast']['active'] = false;
                $current_interface['multicast']['active'] = false;
                $current_interface['loopback']['active'] = false;
                foreach ($values as $value) {
                    // echo Functions::getExecutionTime().'<br>';

                    //ligne 1
                        //on cherche pour les connexion ethernet
                        if(preg_match($this->getSpecialized("network-ethernetNetwork"), $key)){
                            $current_interface['type'] = "Ethernet";
                        }

                        //on cherche pour les connexion wifi
                        if(preg_match($this->getSpecialized("network-wirelessNetwork"), $key)){
                            $current_interface['type'] = "Wifi";
                        }

                        //on cherche pour les boucles locales
                        if(preg_match($this->getSpecialized("network-localLoopbackNetwork"), $value)){
                            $current_interface['type'] = "Locale_loopback";
                        }

                        //on cherche l'adresse mac
                        if(preg_match($this->getSpecialized("network-macAddress"), $value, $matches)){
                            $current_interface['mac'] = $matches[1];
                        }

                    //ligne 2
                        //on cherche l'ip locale
                        if(preg_match($this->getSpecialized("network-localeIP"), $value, $matches))
                            $current_interface['local_ip'] = $matches[1];

                        //on cherche l'ip de broadcast
                        if(preg_match($this->getSpecialized("network-broadcastIP"), $value, $matches))
                            $current_interface['broadcast']['ip'] = $matches[1];

                        //on cherche l'ip de broadcast
                        if(preg_match($this->getSpecialized("network-netMask"), $value, $matches))
                            $current_interface['masque'] = $matches[1];

                    //ligne 3
                        //on regarde les services activés :
                        $current_interface['active'] = preg_match($this->getSpecialized("network-interfaceUp"), $value)? true : $current_interface['active'];
                        $current_interface['broadcast']['active'] = preg_match($this->getSpecialized("network-broadcastUp"), $value)? true :  $current_interface['broadcast']['active'];
                        $current_interface['multicast']['active'] = preg_match($this->getSpecialized("network-multicastUp"), $value)? true :  $current_interface['multicast']['active'];
                        $current_interface['loopback']['active'] = preg_match($this->getSpecialized("network-loopbackUp"), $value)? true :  $current_interface['loopback']['active'];

                        //MTU
                        if(preg_match($this->getSpecialized("network-MTU"), $value, $matches))
                            $current_interface['MTU'] = $matches[1];

                        //metric
                        if(preg_match($this->getSpecialized("network-metric"), $value, $matches))
                            $current_interface['metric'] = $matches[1];
                    //ligne 4
                    if(preg_match($this->getSpecialized("network-receivedTransmitted"), $value, $match)){
                        if(preg_match($this->getSpecialized("network-receivedTransmittedPacket"), $value, $matches))
                            $current_interface[$match[1]]['packets'] = $matches[1];

                        if(preg_match($this->getSpecialized("network-receivedTransmittedErrors"), $value, $matches))
                            $current_interface[$match[1]]['errors'] = $matches[1];

                        if(preg_match($this->getSpecialized("network-receivedTransmittedDropped"), $value, $matches))
                            $current_interface[$match[1]]['dropped'] = $matches[1];

                        if(preg_match($this->getSpecialized("network-receivedTransmittedOverruns"), $value, $matches))
                            $current_interface[$match[1]]['overruns'] = $matches[1];

                        if(preg_match($this->getSpecialized("network-receivedTransmittedBytes"), $value, $matches)){
                            $current_interface["RX"]['bytes'] = $matches[1];
                            $current_interface["RX"]['r_bytes'] = $matches[2];
                            $current_interface["TX"]['bytes'] = $matches[3];
                            $current_interface["TX"]['r_bytes'] = $matches[4];
                        }
                    }

                }
                //on ajoute l'ip externe
                $return['extern_ip'] = rtrim(file_get_contents("http://icanhazip.com/"));
                $return[$key] = $current_interface;
            }
            return $return;
    }
}