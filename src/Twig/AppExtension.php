<?php declare(strict_types=1);

namespace App\Twig;

use App\Component\Util\Date;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('rusDate', [$this, 'rusDate']),
        ];
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('area', [$this, 'calculateArea']),
        ];
    }

    /**
     * @param int $width
     * @param int $length
     *
     * @return float|int
     */
    public function calculateArea(int $width, int $length)
    {
        return $width * $length;
    }

    /**
     * @param \DateTime $date
     *
     * @return string
     */
    public function rusDate(\DateTime $date): string
    {
       return Date::rusFormat($date);
    }
}
