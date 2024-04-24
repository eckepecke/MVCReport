<?php

namespace App\Cards;

class CardGraphic extends Card
{
    private string $imgPath;

    public function __construct()
    {
        parent::__construct();

        $this->imgPath = $this->getImgName();
    }

    public function getImgName(): string
    {
        return $this->suit . '_' . $this->value . '.svg';
    }

    public function setSuit(string $suit): void
    {
        $this->suit = $suit;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }
}
