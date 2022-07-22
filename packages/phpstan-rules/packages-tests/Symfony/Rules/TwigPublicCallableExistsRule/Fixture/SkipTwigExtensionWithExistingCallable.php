<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\TwigPublicCallableExistsRule\Fixture;

use Twig\Extension\AbstractExtension;
use Twig_SimpleFunction;

final class SkipTwigExtensionWithExistingCallable extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('thatWasGreat', [$this, 'thatWasGreat']),
        ];
    }

    public function thatWasGreat()
    {
        return 'thatWasGreat';
    }
}
