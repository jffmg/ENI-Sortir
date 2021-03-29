<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity("mail")
 * @UniqueEntity("motPasse")
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(name="mail", type="string", length=255, unique=true)
     */
    private $mail;

    /**
     * @ORM\Column(name="motPasse", type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $admin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Event")
     * @ORM\JoinTable(name="participantEvents")
     */
    private $eventsParticipant;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="organizer")
     */
    private $eventsOrganizer;

    /**
     * @var Campus
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus", inversedBy="participants")
     */
    private $campus;

    /*CONSTRUCTEUR*/

    public function __construct()
    {
        $this->eventsParticipant = new ArrayCollection();
        $this->eventsOrganizer = new ArrayCollection();
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
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName): void
    {
        $this->userName = $userName;
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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param mixed $admin
     */
    public function setAdmin($admin): void
    {
        $this->admin = $admin;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

    /**
     * @return ArrayCollection
     */
    public function getEventsParticipant(): ArrayCollection
    {
        return $this->eventsParticipant;
    }

    /**
     * @param ArrayCollection $eventsParticipant
     */
    public function setEventsParticipant(ArrayCollection $eventsParticipant): void
    {
        $this->eventsParticipant = $eventsParticipant;
    }

    /**
     * @return ArrayCollection
     */
    public function getEventsOrganizer(): ArrayCollection
    {
        return $this->eventsOrganizer;
    }

    /**
     * @param ArrayCollection $eventsOrganizer
     */
    public function setEventsOrganizer(ArrayCollection $eventsOrganizer): void
    {
        $this->eventsOrganizer = $eventsOrganizer;
    }

    /**
     * @return Campus
     */
    public function getCampus(): Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus $campus
     */
    public function setCampus(Campus $campus): void
    {
        $this->campus = $campus;
    }


    public function getRoles()
    {
        return ["ROLE_USER"];
    }

    // Unused methods
    public function getSalt(){}
    public function eraseCredentials(){}
}
