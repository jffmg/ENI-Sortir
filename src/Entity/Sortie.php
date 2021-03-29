<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
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
     * @ORM\Column(type="datetime")
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="integer")
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $infosSortie;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant")
     * @ORM\JoinTable(name="sortieParticipants")
     */
    private $participants;

    /**
     * @var Participant
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant", inversedBy="sortiesOrganisateur")
     */
    private $organisateur;

    /**
     * @var Campus
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus", inversedBy="sortiesCampus")
     */
    private $siteOrganisateur;

    /**
     * @var Etat
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat", inversedBy="sorties")
     */
    private $etat;

    /**
     * @var Lieu
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="sorties")
     */
    private $lieu;

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
    public function getDateHeureDebut()
    {
        return $this->dateHeureDebut;
    }

    /**
     * @param mixed $dateHeureDebut
     */
    public function setDateHeureDebut($dateHeureDebut)
    {
        $this->dateHeureDebut = $dateHeureDebut;
    }

    /**
     * @return mixed
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * @param mixed $duree
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;
    }

    /**
     * @return mixed
     */
    public function getDateLimiteInscription()
    {
        return $this->dateLimiteInscription;
    }

    /**
     * @param mixed $dateLimiteInscription
     */
    public function setDateLimiteInscription($dateLimiteInscription)
    {
        $this->dateLimiteInscription = $dateLimiteInscription;
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
    public function setNbInscriptionsMax($nbInscriptionsMax)
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;
    }

    /**
     * @return mixed
     */
    public function getInfosSortie()
    {
        return $this->infosSortie;
    }

    /**
     * @param mixed $infosSortie
     */
    public function setInfosSortie($infosSortie)
    {
        $this->infosSortie = $infosSortie;
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
     * @return Participant
     */
    public function getOrganisateur()
    {
        return $this->organisateur;
    }

    /**
     * @param Participant $organisateur
     */
    public function setOrganisateur($organisateur)
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return Campus
     */
    public function getSiteOrganisateur()
    {
        return $this->siteOrganisateur;
    }

    /**
     * @param Campus $siteOrganisateur
     */
    public function setSiteOrganisateur($siteOrganisateur)
    {
        $this->siteOrganisateur = $siteOrganisateur;
    }

    /**
     * @return Etat
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param Etat $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    /**
     * @return Lieu
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * @param Lieu $lieu
     */
    public function setLieu($lieu)
    {
        $this->lieu = $lieu;
    }




}
