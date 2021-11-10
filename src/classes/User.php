<?php


namespace App;


class User
{
    private $name;
    private $email;
    private $pass;
    private $age;

    /**
     * User constructor.
     * @param $name
     * @param $email
     * @param $pass
     * @param $age
     */
    public function __construct($name = null, $email = null, $pass = null, $age = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->pass = $pass;
        $this->age = $age;
    }


    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age): void
    {
        $this->age = $age;
    }

}