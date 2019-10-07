<?php declare(strict_types=1);

namespace App\Entity\VO\History;

class JoinData implements HistoryDataInterface
{
    /**
     * @var string
     */
    private $nickname;

    /**
     * @param string $nickname
     */
    public function __construct(string $nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return string
     */
    public function getNickname(): string
    {
        return $this->nickname;
    }

    /**
     * @return JoinData
     */
    public static function fromArray(array $data): HistoryDataInterface
    {
        return new self($data['nickname']);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}
