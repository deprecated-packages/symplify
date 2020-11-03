<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

abstract class AbstractClassWithConstructorInjection
{
    protected $config;

    public function __construct($configuration)
    {
        $this->config = $configuration;
    }
}
