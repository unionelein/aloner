<?php declare(strict_types=1);

namespace App\Component\Model\DTO\EventPartyHistory;

class LeaveHistory implements HistoryDataInterface
{
    /**
     * @return LeaveHistory
     */
    public static function fromArray(array $data): HistoryDataInterface
    {
        return new self();
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}