<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher\Data;

use Webmozart\Assert\Assert;

abstract class PusherData implements \JsonSerializable
{
    public const TYPE_JOIN = 'join';

    public const TYPE_SKIP = 'skip';

    public const TYPE_FILLED = 'filled';

    public const TYPE_MO_OFFER = 'mo_offer';

    public const TYPE_MO_ANSWER = 'mo_answer';

    public const TYPE_MO_ACCEPTED = 'mo_accepted';

    public const TYPES = [
        self::TYPE_JOIN,
        self::TYPE_SKIP,
        self::TYPE_FILLED,
        self::TYPE_MO_OFFER,
        self::TYPE_MO_ANSWER,
        self::TYPE_MO_ACCEPTED,
    ];

    /** @var string */
    protected $type;

    /** @var string */
    protected $topic;

    /**
     * @param string $type
     * @param string $topic
     */
    public function __construct(string $type, string $topic)
    {
        Assert::oneOf($type, self::TYPES);

        $this->type  = $type;
        $this->topic = $topic;
    }

    /**
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
