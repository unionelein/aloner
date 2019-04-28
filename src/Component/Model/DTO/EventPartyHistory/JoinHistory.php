<?php declare(strict_types=1);

namespace App\Component\Model\DTO\EventPartyHistory;

class JoinHistory implements HistoryDataInterface
{
    private $nickname;

    public function __construct(string $nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return JoinHistory
     */
    public static function fromArray(array $data): HistoryDataInterface
    {
        return new self($data['nickname']);
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}