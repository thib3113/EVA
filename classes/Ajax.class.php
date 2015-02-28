<?php

Class ajax{
    public $response = array(
        "status" => false,
        "message" => "erreur inconnue",
    );

    public function get_response(){
        return $this->response;
    }

    public function set_response($response){
        $response['status'] = ($response['status'])? true : false;

        $this->response = $response;
    }
}