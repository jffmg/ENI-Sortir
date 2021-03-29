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
    private $nom;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Participant", mappedBy="campus")
     */
    private $participants;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="siteOrganisateur")
     */
    private $sortiesCampus;

    /*CONSTRUCTEUR*/

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->sortiesCampus = new ArrayCollection();
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
    public function getSortiesCampus()
    {
        return $this->sortiesCampus;
    }

    /**
     * @param ArrayCollection $sortiesCampus
     */
    public function setSortiesCampus($sortiesCampus)
    {
        $this->sortiesCampus = $sortiesCampus;
    }




}
