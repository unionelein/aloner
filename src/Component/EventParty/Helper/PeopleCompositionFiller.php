<?php declare(strict_types=1);

namespace App\Component\EventParty\Helper;

use App\Entity\VO\PeopleComposition;
use App\Entity\VO\Range;

class PeopleCompositionFiller
{
    /**
     * @param Range $range
     *
     * @return PeopleComposition
     */
    public static function fillFromRange(Range $range): PeopleComposition
    {
        $minEachSex = (int) ceil($range->getMin() / 2);
        $maxEachSex = (int) floor($range->getMax() / 2);

        // If range min equal range max and it is not even number,
        // then minEachSex will be greater than maxEachSex.
        // Example: if min=3 and max=3 then minEachSex=2 and maxEachSex=1.
        if ($minEachSex > $maxEachSex) {
            // girls more than guys on Earth :D
            return new PeopleComposition($maxEachSex, $minEachSex);
        }

        $eachSex = random_int($minEachSex, $maxEachSex);

        return new PeopleComposition($eachSex, $eachSex);
    }
}