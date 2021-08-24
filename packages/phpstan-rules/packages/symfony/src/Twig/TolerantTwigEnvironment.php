<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Twig;

use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Allows any function and filter
 */
final class TolerantTwigEnvironment extends Environment
{
    public function getFilter(string $name): ?TwigFilter
    {
        return new TwigFilter($name);
    }

    public function getFunction(string $name): ?TwigFunction
    {
        return new TwigFunction($name);
    }
}
