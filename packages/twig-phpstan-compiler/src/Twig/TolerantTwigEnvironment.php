<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\Twig;

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
        $twigFilter = parent::getFilter($name);
        if ($twigFilter instanceof TwigFilter) {
            return $twigFilter;
        }

        // 2nd argument is dummy function, so the function name is not empty and compilation twig to PHP passes
        return new TwigFilter($name, fn ($value) => $value);
    }

    public function getFunction(string $name): ?TwigFunction
    {
        $twigFunction = parent::getFunction($name);
        if ($twigFunction instanceof TwigFunction) {
            return $twigFunction;
        }

        // 2nd argument is dummy function, so the function name is not empty and compilation twig to PHP passes
        return new TwigFunction($name, fn ($value) => $value);
    }
}
