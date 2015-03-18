<?php

Class Widget extends SgdbManager{

    public $name;
    public $id;
    public $width = 4;

    public function __construct(){
        foreach (debug_backtrace()[0]["args"] as $arg_key => $arg) {
            foreach ($arg as $key => $value) {
                if(method_exists($this,"set".ucfirst($key)))
                    $this->{"set".ucfirst($key)}($value);
            }
        }
        var_dump($this);
    }

    public function export(){
        return array(
            "name"  => $this->name,
            "id"    => $this->id,
            "width" => $this->width
            );
    }

    private function generate_id(){
        return md5($this->name.time());
    }

    /**
     * Gets the value of name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param mixed $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = !empty($id)?$id:$this->generate_id();

        return $this;
    }

    /**
     * Gets the value of width.
     *
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Sets the value of width.
     *
     * @param mixed $width the width
     *
     * @return self
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }
}