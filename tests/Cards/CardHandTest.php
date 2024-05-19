<?php

namespace App\Card;
use App\Cards\CardGraphic;
use App\Cards\CardHand;



use PHPUnit\Framework\TestCase;

/**
 * Test cases for class CardHand.
 */
class CardHandTest extends TestCase
{

    /**
     * @var CardHand
     */
    private $cardHand;

    /**
     * Set up the test environment before each test method.
     */
    public function setUp(): void
    {
        $this->cardHand = new CardHand();
    }
    public function testGetNumberCards(): void
    {
        $initial = $this->cardHand->getNumberCards();
        $exp = 0;

        $this->assertSame($exp, $initial);

        $card1 = new CardGraphic();
        $card2 = new CardGraphic();

        $this->cardHand->add($card1);
        $this->cardHand->add($card2);

        $res = $this->cardHand->getNumberCards();

        $this->assertSame(2, $res);

    }



    public function testGetValues()
    {

        $stub1 = $this->createMock(CardGraphic::class);

        // Configure the stub.
        $stub1->method('getCardString')
            ->willReturn('aceSpades');

        $stub2 = $this->createMock(CardGraphic::class);

        // Configure the stub.
        $stub2->method('getCardString')
            ->willReturn('aceHearts');

        $this->cardHand->add($stub1);
        $this->cardHand->add($stub2);

        $res = $this->cardHand->getCardValues();
        $exp = ['aceSpades', 'aceHearts'];

        $this->assertSame($exp, $res);
    }

    public function testGetImgNames()
    {

        $stub1 = $this->createMock(CardGraphic::class);

        // Configure the stub.
        $stub1->method('getImgName')
            ->willReturn('aceSpades.jpg');

        $stub2 = $this->createMock(CardGraphic::class);

        // Configure the stub.
        $stub2->method('getImgName')
            ->willReturn('aceHearts.jpg');

        $this->cardHand->add($stub1);
        $this->cardHand->add($stub2);

        $res = $this->cardHand->getImgNames();
        $exp = ['aceSpades.jpg', 'aceHearts.jpg'];

        $this->assertSame($exp, $res);
    }

    public function testGetHand()
    {
        $stub1 = $this->createMock(CardGraphic::class);
        $stub2 = $this->createMock(CardGraphic::class);

        // Add the mocked cards to the hand
        $this->cardHand->add($stub1);
        $this->cardHand->add($stub2);

        // Call the getHand method
        $hand = $this->cardHand->getHand();

        // Assert that the hand contains the same objects that were added
        $this->assertCount(2, $hand);
        $this->assertSame($stub1, $hand[0]);
        $this->assertSame($stub2, $hand[1]);
    }
}