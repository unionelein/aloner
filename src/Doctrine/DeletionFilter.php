<?php declare(strict_types=1);

namespace App\Doctrine;

use App\Entity\EventParty;
use App\Entity\EPHistory;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DeletionFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        return '';
    }
}
