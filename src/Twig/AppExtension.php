<?php declare(strict_types=1);

namespace App\Twig;

use App\Component\Util\Date;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('dateToString', [$this, 'dateToString']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('area', [$this, 'calculateArea']),
        ];
    }

    public function calculateArea(int $width, int $length)
    {
        return $width * $length;
    }

    public function dateToString(\DateTime $date): string
    {
       return Date::convertDateToString($date);
    }
}