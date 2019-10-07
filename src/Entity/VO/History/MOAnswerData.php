<?php declare(strict_types=1);

namespace App\Entity\VO\History;

use App\Entity\VO\MeetingOptions;
use Webmozart\Assert\Assert;

class MOAnswerData implements HistoryDataInterface
{
    /** @var bool */
    private $answer;

    /** @var null|string */
    private $newMeetingPlace;

    /** @var null|\DateTime */
    private $newMeetingAt;

    /**
     * @param bool                $answer
     * @param MeetingOptions|null $newMO
     */
    public function __construct(bool $answer, MeetingOptions $newMO = null)
    {
        if (false === $answer) {
            Assert::notNull($newMO, 'New meeting options must be provided in case reject answer');

            $this->newMeetingPlace = $newMO->getMeetingPlace();
            $this->newMeetingAt    = $newMO->getMeetingAt();
        }

        $this->answer  = $answer;
    }

    /**
     * @return bool
     */
    public function getAnswer(): bool
    {
        return $this->answer;
    }

    /**
     * @return string|null
     */
    public function getNewMeetingPlace(): ?string
    {
        return $this->newMeetingPlace;
    }

    /**
     * @return \DateTime|null
     */
    public function getNewMeetingAt(): ?\DateTime
    {
        return $this->newMeetingAt;
    }

    /**
     * @return MOAnswerData
     */
    public static function fromArray(array $data): HistoryDataInterface
    {
        $answer = $data['answer'];
        $newMO  = null;

        if (false === $answer) {
            $date  = new \DateTime($data['newMeetingAt']['date']);
            $place = $data['newMeetingPlace'];

            $newMO = new MeetingOptions($date, $place);
        }

        return new self($answer, $newMO);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}
