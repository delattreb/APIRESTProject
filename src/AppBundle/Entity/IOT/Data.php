<?php

namespace AppBundle\Entity\IOT;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Datas")
 * )
 */
class Data
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="float")
     */
    protected $temperature;

    /**
     * @ORM\Column(type="float")
     */
    protected $humidity;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTemperature()
    {
        return $this->temperature;
    }

    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;
    }

    public function getHumidity()
    {
        return $this->humidity;
    }

    public function setHumidity($humidity)
    {
        $this->humidity = $humidity;
    }
}