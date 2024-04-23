<?php

namespace App\Poker;

class ActionSequence
{
    private $preflopSequence;
    private $flopSequence;
    private $turnSequence;
    private $riverSequence;


    public function __construct()
    {
        $this->preflopSequence = [];
        $this->flopSequence = [];
        $this->turnSequence = [];
        $this->riverSequence = [];

    }

    public function addAction(int $streetNum, string $action): void
    {
        if ($streetNum === 1) {
            $this->preflopSequence[] = $action;
        } elseif ($streetNum === 2) {
            $this->flopSequence[] = $action;
        } elseif ($streetNum === 3) {
            $this->turnSequence[] = $action;
        } elseif ($streetNum === 2) {
            $this->riverSequence[] = $action;
        }
    }

    public function getCurrentStreetAction(int $streetNum): array
    {
        if ($streetNum === 1) {
            return $this->preflopSequence;
        } elseif ($streetNum === 2) {
            return $this->flopSequence;
        } elseif ($streetNum === 3) {
            return $this->turnSequence;
        } elseif ($streetNum === 4) {
            return $this->riverSequence;
        }
    }

    public function addPreflopAction(string $action): void
    {
        $this->preflopSequence[] = $action;
    }

    public function addFlopAction(string $action): void
    {
        $this->flopSequence[] = $action;
    }

    public function addTurnAction(string $action): void
    {
        $this->turnSequence[] = $action;
    }

    public function addRiverAction(string $action): void
    {
        $this->riverSequence[] = $action;
    }

    public function resetALLSequences()
    {
        $this->preflopSequence = [];
        $this->flopSequence = [];
        $this->turnSequence = [];
        $this->riverSequence = [];
    }

    public function getPreflopSequence()
    {
        return $this->preflopSequence;
    }
    public function getFlopSequence()
    {
        return $this->flopSequence;
    }
    public function getTurnSequence()
    {
        return $this->turnSequence;
    }
    public function getRiverSequence()
    {
        return $this->riverSequence;
    }
}
