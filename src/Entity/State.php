<?php

namespace App\Entity;

use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=StateRepository::class)
 */
class State
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(max=255, maxMessage="255 caractères maximum")
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @Assert\Length(max=10, maxMessage="10 caractères maximum")
     * @ORM\Column(type="string", length=10)
     */
    private $shortLabel;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="state",cascade="remove")
     */
    private $events;

    /*CONSTRUCTEUR*/

    public function __construct()
    {
        $this->events = new ArrayCollection();
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
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getShortLabel()
    {
        return $this->shortLabel;
    }

    /**
     * @param mixed $shortLabel
     */
    public function setShortLabel($shortLabel): void
    {
        $this->shortLabel = $shortLabel;
    }

    /**
     * @return ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param ArrayCollection $events
     */
    public function setEvents($events)
    {
        $this->events = $events;
    }



}
