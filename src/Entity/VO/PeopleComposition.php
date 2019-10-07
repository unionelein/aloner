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
class PeopleComposition
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="num_of_girls")
     */
    private $numberOfGirls;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="num_of_guys")
     */
    private $numberOfGuys;

    /**
     * EPComposition constructor.
     *
     * @param int $numberOfGirls
     * @param int $numberOfGuys
     */
    public function __construct(int $numberOfGirls, int $numberOfGuys)
    {
        $this->numberOfGirls = $numberOfGirls;
        $this->numberOfGuys  = $numberOfGuys;
    }

    /**
     * @return int
     */
    public function getNumberOfGirls(): int
    {
        return $this->numberOfGirls;
    }

    /**
     * @return int
     */
    public function getNumberOfGuys(): int
    {
        return $this->numberOfGuys;
    }

    /**
     * @return int
     */
    public function getNumberOfPeople(): int
    {
        return $this->numberOfGuys + $this->numberOfGirls;
    }

    /**
     * @param Sex $sex
     *
     * @return int
     */
    public function getNumberOf(Sex $sex): int
    {
        if ($sex->isMale()) {
            return $this->numberOfGuys;
        }

        if ($sex->isFemale()) {
            return $this->numberOfGirls;
        }

        throw new \InvalidArgumentException('Invalid sex type given');
    }
}
