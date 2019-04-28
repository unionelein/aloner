<?php declare(strict_types=1);

namespace App\Component\Model\DTO\EventPartyHistory;

interface HistoryDataInterface
{
    public static function fromArray(array $data): HistoryDataInterface;

    public function toArray(): array;
}