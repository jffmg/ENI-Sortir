<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity(fields={"mail"}, message="Cette adresse email est déjà utilisée")
 * @UniqueEntity(fields={"userName"}, message="Ce pseudo n'est pas disponible")
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
     * @Assert\Length(max=255, maxMessage="255 caractères maximum")
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $userName;

    /**
     * @Assert\Length(max=255, maxMessage="255 caractères maximum")
    * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\Length(max=255, maxMessage="255 caractères maximum")
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @Assert\Regex(
     *     pattern     = "/^0[1-9](((?:[\s.-]?)(\d\d)){4})/i",
     *     htmlPattern = "/0[1-9](((?:[\s.-]?)(\d\d)){4})",
     *     message = "Ce numéro n'est pas valide"
     * )
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $phone;

    /**
     * @Assert\Email(message="Ceci n'est pas un email valide")
     * @Assert\Length(max=255, maxMessage="255 caractères max")
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="mail", type="string", length=255, unique=true)
     */
    private $mail;

    /**
     * @Assert\Length(max=255, maxMessage="255 caractères maximum")
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="password", type="string", length=255)
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
     * @ORM\JoinColumn(nullable=false)
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
