<?php declare(strict_types=1);

namespace App\Event;

use App\Entity\EPOfferMOHistory;
use Symfony\Contracts\EventDispatcher\Event;

class MOOfferedEvent extends Event
{
    /** @var EPOfferMOHistory */
    private $offer;

    /**
     * @param EPOfferMOHistory $offer
     */
    public function __construct(EPOfferMOHistory $offer)
    {
        $this->offer = $offer;
    }

    /**
     * @return EPOfferMOHistory
     */
    public function getOffer(): EPOfferMOHistory
    {
        return $this->offer;
    }
}
