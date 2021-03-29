<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 */
class City
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $zipCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Location", mappedBy="city")
     */
    private $locations;

    /*CONSTRUCTEUR*/

    public function __construct()
    {
        $this->locations= new ArrayCollection();
    }



    /*GETTERS & SETTERS*/

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     */
    public function setCodePostal($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return ArrayCollection
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @param ArrayCollection $locations
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;
    }



}
