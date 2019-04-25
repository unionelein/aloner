<?php declare(strict_types=1);

namespace App\Doctrine;

use App\Entity\EventParty;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DeletedEventPartyFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->getReflectionClass()->name != EventParty::class) {
            return '';
        }

        return \sprintf('%s.status != %d', $targetTableAlias, EventParty::STATUS_DELETED);
    }
}