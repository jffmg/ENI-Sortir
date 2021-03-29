<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CampusRepository::class)
 */
class Campus
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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Participant", mappedBy="campus")
     */
    private $participants;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="campusOrganizer")
     */
    private $eventsCampus;

    /*CONSTRUCTEUR*/

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->eventsCampus = new ArrayCollection();
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
     * @return ArrayCollection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param ArrayCollection $participants
     */
    public function setParticipants($participants)
    {
        $this->participants = $participants;
    }

    /**
     * @return ArrayCollection
     */
    public function getEventsCampus()
    {
        return $this->eventsCampus;
    }

    /**
     * @param ArrayCollection $eventsCampus
     */
    public function setEventsCampus($eventsCampus)
    {
        $this->eventsCampus = $eventsCampus;
    }




}
