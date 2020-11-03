<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Fixture;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

class SkipParameterProvider
{
    public function __construct(ParameterProvider $parameterProvider, ParameterBagInterface $parameterBag)
    {
        $value = $parameterProvider->provideArrayParameter('...');
        $value2 = $parameterBag->get('...');
    }
}
