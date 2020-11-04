<?php

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenConstructorDependencyByTypeRule\Fixture;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PassContainerToConstructorParameter
{
    public function __construct(ContainerInterface $container)
    {
    }
}
