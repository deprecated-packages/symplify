<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

abstract class AbstractClassWithAutowireInjection
{
    protected $config;

    /**
     * @required
     */
    public function autowireAbstractClassWithAutowireInjection($configuration)
    {
        $this->config = $configuration;
    }
}