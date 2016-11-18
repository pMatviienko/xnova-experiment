<?php

namespace AuthBundle\Entity;
use FOS\UserBundle\Model\Group as BaseGroup;
/**
 * Group
 */
class Group extends BaseGroup
{
    public function __construct($name=null, $roles = array())
    {
        $this->name = !empty($name) ? $name : $this->name;
        $this->roles = !empty($roles) ? $roles : $this->roles;
        if(null == $this->roles){
            $this->roles = array();
        }
    }
    /**
     * @var integer
     */
    protected $id;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
       return $this->getName();
    }
}

