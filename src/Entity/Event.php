<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Veuillez indiquer un intitulé")
     * @Assert\Length(max=255, maxMessage="255 caractères maximum")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\NotBlank(message="Veuillez indiquer une date et une heure")
     * @ORM\Column(type="datetime")
     */
    private $dateTimeStart;

    /**
     * @Assert\NotBlank(message="Veuillez indiquer une durée en minutes")
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @Assert\NotBlank(message="Veuillez indiquer une date et une heure")
     * @Assert\LessThan(propertyPath="dateTimeStart", message="Les inscriptions doivent être closes avant le début de l'activité")
     * @ORM\Column(type="datetime")
     */
    private $dateEndInscription;

    /**
     * @Assert\NotBlank(message="Veuillez indiquer un nombre de places")
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;

    /**
     * @Assert\Length(max=255, maxMessage="255 caractères maximum")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $infosEvent;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant",cascade="remove")
     * @ORM\JoinTable(name="eventParticipants")
     */
    private $participants;

    /**
     * @var Participant
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant", inversedBy="eventsOrganizer")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $organizer;

    /**
     * @var Campus
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus", inversedBy="eventsCampus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campusOrganizer;

    /**
     * @var State
     * @ORM\ManyToOne(targetEntity="App\Entity\State", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private $location;

    /*CONSTRUCTEUR*/

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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
    public function setId($id): void
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
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDateTimeStart()
    {
        return $this->dateTimeStart;
    }

    /**
     * @param mixed $dateTimeStart
     */
    public function setDateTimeStart($dateTimeStart): void
    {
        $this->dateTimeStart = $dateTimeStart;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return mixed
     */
    public function getDateEndInscription()
    {
        return $this->dateEndInscription;
    }

    /**
     * @param mixed $dateEndInscription
     */
    public function setDateEndInscription($dateEndInscription): void
    {
        $this->dateEndInscription = $dateEndInscription;
    }

    /**
     * @return mixed
     */
    public function getNbInscriptionsMax()
    {
        return $this->nbInscriptionsMax;
    }

    /**
     * @param mixed $nbInscriptionsMax
     */
    public function setNbInscriptionsMax($nbInscriptionsMax): void
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;
    }

    /**
     * @return mixed
     */
    public function getInfosEvent()
    {
        return $this->infosEvent;
    }

    /**
     * @param mixed $infosEvent
     */
    public function setInfosEvent($infosEvent): void
    {
        $this->infosEvent = $infosEvent;
    }

    /**
     * @return Participant
     */
    public function getOrganizer(): Participant
    {
        return $this->organizer;
    }

    /**
     * @param Participant $organizer
     */
    public function setOrganizer(Participant $organizer): void
    {
        $this->organizer = $organizer;
    }

    /**
     * @return Campus
     */
    public function getCampusOrganizer(): Campus
    {
        return $this->campusOrganizer;
    }

    /**
     * @param Campus $campusOrganizer
     */
    public function setCampusOrganizer(Campus $campusOrganizer): void
    {
        $this->campusOrganizer = $campusOrganizer;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * @param State $state
     */
    public function setState(State $state): void
    {
        $this->state = $state;
    }

    /**
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation(Location $location): void
    {
        $this->location = $location;
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

    public function addParticipant(Participant $participant)
    {
        if ($this->participants->contains($participant)) {
            return;
        }
        $this->participants[] = $participant;

    }

    public function removeParticipant(Participant $participant)
    {
        $this->participants->removeElement($participant);
    }

}
