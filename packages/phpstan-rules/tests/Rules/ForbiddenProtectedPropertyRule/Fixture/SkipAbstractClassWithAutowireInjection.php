<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

abstract class SkipAbstractClassWithAutowireInjection
{
    protected $config;

    /**
     * @required
     */
    public function autowireSkipAbstractClassWithAutowireInjection($configuration)
    {
        $this->config = $configuration;
    }
}
