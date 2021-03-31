<?php

namespace App\Entity;

use Cassandra\Date;

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
     * @return Campus
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param Campus $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     */
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param string $endDate
     */
    public function setEndDate($endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return bool
     */
    public function isUserIsOrganizer()
    {
        return $this->userIsOrganizer;
    }

    /**
     * @param bool $userIsOrganizer
     */
    public function setUserIsOrganizer($userIsOrganizer): void
    {
        $this->userIsOrganizer = $userIsOrganizer;
    }

    /**
     * @return bool
     */
    public function isUserIsRegistered()
    {
        return $this->userIsRegistered;
    }

    /**
     * @param bool $userIsRegistered
     */
    public function setUserIsRegistered($userIsRegistered): void
    {
        $this->userIsRegistered = $userIsRegistered;
    }

    /**
     * @return bool
     */
    public function isUserIsNotRegistered()
    {
        return $this->userIsNotRegistered;
    }

    /**
     * @param bool $userIsNotRegistered
     */
    public function setUserIsNotRegistered($userIsNotRegistered): void
    {
        $this->userIsNotRegistered = $userIsNotRegistered;
    }

    /**
     * @return bool
     */
    public function isEndedEvents()
    {
        return $this->endedEvents;
    }

    /**
     * @param bool $endedEvents
     */
    public function setEndedEvents($endedEvents): void
    {
        $this->endedEvents = $endedEvents;
    }


}
