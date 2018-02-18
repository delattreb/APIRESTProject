<?php

namespace AppBundle\Entity\IOT;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Customers",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="customers_name_unique",columns={"name"}),
 *                         @ORM\UniqueConstraint(name="customers_email_unique",columns={"email"})}
 * )
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=25)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $state;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $postalcode;

    /**
     * @ORM\OneToMany(targetEntity="Device", mappedBy="customer", cascade={"persist", "remove"})
     * @var Device[]
     */
    protected $devices;

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    public function getPostalcode()
    {
        return $this->postalcode;
    }

    public function setPostalcode($postalcode)
    {
        $this->postalcode = $postalcode;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDevices()
    {
        return $this->devices;
    }

    public function setDevices($devices)
    {
        $this->devices = $devices;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}