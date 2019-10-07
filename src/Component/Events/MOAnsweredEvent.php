<?php declare(strict_types=1);

namespace App\Component\Events;

use App\Entity\EPAnswerMOHistory;
use Symfony\Component\EventDispatcher\Event;

class MOAnsweredEvent extends Event
{
    /** @var EPAnswerMOHistory */
    private $answer;

    /**
     * @param EPAnswerMOHistory $answer
     */
    public function __construct(EPAnswerMOHistory $answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return EPAnswerMOHistory
     */
    public function getAnswer(): EPAnswerMOHistory
    {
        return $this->answer;
    }
}
