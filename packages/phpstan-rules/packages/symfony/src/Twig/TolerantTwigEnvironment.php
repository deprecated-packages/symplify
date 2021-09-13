<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Twig;

use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Allows any function and filter
 */
final class TolerantTwigEnvironment extends Environment
{
    public function __construct()
    {
        // to avoid DI injection trigger on empty dependency
        parent::__construct(new ArrayLoader());
    }

    public function getFilter(string $name): ?TwigFilter
    {
        return new TwigFilter($name, $name);
    }

    public function getFunction(string $name): ?TwigFunction
    {
        return new TwigFunction($name, $name);
    }
}
