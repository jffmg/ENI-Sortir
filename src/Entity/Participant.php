<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity("mail")
 * @UniqueEntity("motPasse")
 */
class Participant
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(name="mail", type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(name="motPasse", type="string", length=255)
     */
    private $motPasse;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Sortie")
     * @ORM\JoinTable(name="participantSorties")
     */
    private $sortiesParticipant;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="organisateur")
     */
    private $sortiesOrganisateur;

    /**
     * @var Campus
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus", inversedBy="participants")
     */
    private $campus;

    /*CONSTRUCTEUR*/

    public function __construct()
    {
        $this->sortiesParticipant = new ArrayCollection();
        $this->sortiesOrganisateur = new ArrayCollection();
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
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
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
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getMotPasse()
    {
        return $this->motPasse;
    }

    /**
     * @param mixed $motPasse
     */
    public function setMotPasse($motPasse)
    {
        $this->motPasse = $motPasse;
    }

    /**
     * @return mixed
     */
    public function getAdministrateur()
    {
        return $this->administrateur;
    }

    /**
     * @param mixed $administrateur
     */
    public function setAdministrateur($administrateur)
    {
        $this->administrateur = $administrateur;
    }

    /**
     * @return mixed
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * @param mixed $actif
     */
    public function setActif($actif)
    {
        $this->actif = $actif;
    }

    /**
     * @return ArrayCollection
     */
    public function getSortiesParticipant()
    {
        return $this->sortiesParticipant;
    }

    /**
     * @param ArrayCollection $sortiesParticipant
     */
    public function setSortiesParticipant($sortiesParticipant)
    {
        $this->sortiesParticipant = $sortiesParticipant;
    }

    /**
     * @return ArrayCollection
     */
    public function getSortiesOrganisateur()
    {
        return $this->sortiesOrganisateur;
    }

    /**
     * @param ArrayCollection $sortiesOrganisateur
     */
    public function setSortiesOrganisateur($sortiesOrganisateur)
    {
        $this->sortiesOrganisateur = $sortiesOrganisateur;
    }

    /**
     * @return Campus
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param Campus $campus
     */
    public function setCampus($campus)
    {
        $this->campus = $campus;
    }







}
