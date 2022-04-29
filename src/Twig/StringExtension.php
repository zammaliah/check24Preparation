<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StringExtension extends AbstractExtension
{
    const LENGTH=1000;
    const FIRSTCHAR=0;

    /**
     * @return array|TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('shorten', [$this, 'cutString']),
        ];
    }

    /**
     * @param string $path
     * @return string|null
     */
    public function cutString(string $string): ?string
    {
        return $string = mb_strimwidth($string, self::FIRSTCHAR, self::LENGTH, '...');
    }
}