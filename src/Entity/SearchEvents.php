<?php

namespace App\Entity;

use App\Repository\SearchEventsRepository;
use Cassandra\Date;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Campus;

/**
 * @ORM\Entity(repositoryClass=SearchEventsRepository::class)
 */
class SearchEvents
{

    /**
     * @var Campus
     */
    public $campus;

    /**
     * @var string
     */
    public $keywords = '';

    /**
     * @var date
     */
    public $startDate;

    /**
     * @var date
     */
    public $endDate;

    /**
     * @var boolean
     */
    public $userIsOrganizer = false;

    /**
     * @var boolean
     */
    public $userIsRegistered = true;

    /**
     * @var boolean
     */
    public $userIsNotRegistered = true;

    /**
     * @var boolean
     */
    public $endedEvents = false;



    /*GETTERS & SETTERS*/
    /**
     * @return Campus[]
     */
    public function getCampus(): array
    {
        return $this->campus;
    }

    /**
     * @param Campus[] $campus
     */
    public function setCampus(array $campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     */
    public function setStartDate(string $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return string
     */
    public function getEndDate(): string
    {
        return $this->endDate;
    }

    /**
     * @param string $endDate
     */
    public function setEndDate(string $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return bool
     */
    public function isUserIsOrganizer(): bool
    {
        return $this->userIsOrganizer;
    }

    /**
     * @param bool $userIsOrganizer
     */
    public function setUserIsOrganizer(bool $userIsOrganizer): void
    {
        $this->userIsOrganizer = $userIsOrganizer;
    }

    /**
     * @return bool
     */
    public function isUserIsRegistered(): bool
    {
        return $this->userIsRegistered;
    }

    /**
     * @param bool $userIsRegistered
     */
    public function setUserIsRegistered(bool $userIsRegistered): void
    {
        $this->userIsRegistered = $userIsRegistered;
    }

    /**
     * @return bool
     */
    public function isUserIsNotRegistered(): bool
    {
        return $this->userIsNotRegistered;
    }

    /**
     * @param bool $userIsNotRegistered
     */
    public function setUserIsNotRegistered(bool $userIsNotRegistered): void
    {
        $this->userIsNotRegistered = $userIsNotRegistered;
    }

    /**
     * @return bool
     */
    public function isEndedEvents(): bool
    {
        return $this->endedEvents;
    }

    /**
     * @param bool $endedEvents
     */
    public function setEndedEvents(bool $endedEvents): void
    {
        $this->endedEvents = $endedEvents;
    }


}
