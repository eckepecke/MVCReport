<?php
namespace App\Cards;

class CardGraphic extends Card
{
    private $imgPath;

    public function __construct()
    {
        parent::__construct();

        $this->imgPath = $this->getImgName();
    }

    public function getImgName(): string
    {
        return $this->suit . '_' . $this->value . '.svg';
    }

    public function setSuit($suit): void
    {
        $this->suit = $suit;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }
}