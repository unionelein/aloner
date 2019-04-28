<?php declare(strict_types=1);

namespace App\Doctrine;

use App\Entity\EventParty;
use App\Entity\EventPartyHistory;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DeletionFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        switch ($targetEntity->getReflectionClass()->name) {
            case EventPartyHistory::class:
                return  \sprintf('%s.status != %d', $targetTableAlias, EventPartyHistory::STATUS_DELETED);
        }

        return '';
    }
}