<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

use App\Component\Messaging\EventParty\Pusher;

class MeetingPointOfferAnswerData extends AbstractPusherData
{
    /** @var int */
    private $epId;

    /** @var int */
    private $offerId;

    /** @var bool */
    private $answer;

    public function __construct(int $epId, int $offerId, bool $answer)
    {
        parent::__construct(Pusher::TYPE_MEETING_POINT_OFFER_ANSWER);

        $this->epId = $epId;
        $this->offerId = $offerId;
        $this->answer = $answer;
    }

    public function getTopicId(): string
    {
        return (string) $this->epId;
    }

    public function toArray(): array
    {
        return [
            'offerId' => $this->offerId,
            'answer' => $this->answer,
        ];
    }
}