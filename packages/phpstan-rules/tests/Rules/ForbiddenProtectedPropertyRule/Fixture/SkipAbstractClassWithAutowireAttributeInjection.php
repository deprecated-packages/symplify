<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

use Symfony\Contracts\Service\Attribute\Required;

abstract class SkipAbstractClassWithAutowireAttributeInjection
{
    protected $config;

    #[Required]
    public function anything($configuration)
    {
        $this->config = $configuration;
    }
}
