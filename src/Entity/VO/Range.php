<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Entity\VO;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Range
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="min")
     */
    private $min;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="max")
     */
    private $max;

    /**
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max)
    {
        Assert::greaterThanEq($max, $min);

        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }
}
