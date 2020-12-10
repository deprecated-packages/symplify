<?php

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenDependencyByTypeRule\Fixture;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PassContainerToConstructorParameter
{
    public function __construct(ContainerInterface $container)
    {
    }
}
