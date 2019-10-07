<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

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

    /**
     * @return int
     */
    public function randomEven(): int
    {
        $min = (int) \ceil($this->min / 2);
        $max = (int) \floor($this->max / 2);

        return \random_int($min, $max) * 2;
    }
}
