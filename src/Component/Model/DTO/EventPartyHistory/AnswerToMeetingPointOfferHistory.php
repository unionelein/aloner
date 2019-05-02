<?php declare(strict_types=1);

namespace App\Component\Model\DTO\EventPartyHistory;


class AnswerToMeetingPointOfferHistory implements HistoryDataInterface
{
    /** @var int */
    private $offerId;

    /** @var bool */
    private $answer;

    public function __construct(int $offerId, bool $answer)
    {
        $this->offerId = $offerId;
        $this->answer = $answer;
    }

    /**
     * @return AnswerToMeetingPointOfferHistory
     */
    public static function fromArray(array $data): HistoryDataInterface
    {
        return new self($data['offerId'], $data['answer']);
    }

    public function getOfferId(): int
    {
        return $this->offerId;
    }

    public function getAnswer(): bool
    {
        return $this->answer;
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}