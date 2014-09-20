<?php


Class User extends Sqlitemanager{
    protected $id, $name,$pass, $group_id, $email, $create_time;
    protected $TABLE_NAME = "Users";
    protected $object_fields= array(
                                    'id'          =>'key',
                                    'name'        =>'string',
                                    'pass'        => 'string',
                                    'group_id'    => 'int',
                                    'email'       =>'longstring',
                                    'create_time' =>'timestamp',
                                    'email'       =>'longstring'
                                    );

    function __construct($id=null){
        parent::__construct();
        
        if(!empty($id))
            $this->setId($id);

    }

    public function isConnect(){
        return (bool)$this->getId();
    }

    
    

    /**
     * Gets the value of id,.
     *
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }
    
    /**
     * Sets the value of id,.
     *
     * @param mixed $id, the id, 
     *
     * @return self
     */
    protected function setId($id){
        $this->id = $id;

        return $this;
    }
}
?>