<?php declare(strict_types=1);

namespace App\Entity\VO\History;

interface HistoryDataInterface
{
    public static function fromArray(array $data): HistoryDataInterface;

    public function toArray(): array;
}
