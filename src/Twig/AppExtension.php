<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('elision', [$this, 'elisionVowel'])
        ];
    }

    function elisionVowel($word)
    {
        $letter = substr($word, 0,1);
        $vowels = ['a', 'e', 'i', 'o', 'u', 'y', 'h', 'A', 'E', 'I', 'O', 'U', 'Y', 'H'];

        return in_array($letter, $vowels);
    }
}