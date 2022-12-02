<?php

namespace Plugin\RemoteIslandsDeliveryFee\Entity;

class RemoteIslandsDeliveryFee extends \Eccube\Entity\AbstractEntity
{
    private $id;

    private $address;
    
    private $value;

    private $Pref;


    public function getId()
    {
    return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    public function setAddress($address)
    {
        $this->address = $address;
        
        return $this;
    }
    
    public function getAddress()
    {
        return $this->address;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
        
        return $this;
    }
    
    public function getValue()
    {
        return $this->value;
    }

    public function setPref(\Eccube\Entity\Master\Pref $Pref = null)
    {
        $this->Pref = $Pref;

        return $this;
    }

    public function getPref()
    {
        return $this->Pref;
    }
}
