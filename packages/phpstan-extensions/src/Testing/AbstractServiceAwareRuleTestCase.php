<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Testing;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanExtensions\DependencyInjection\PHPStanContainerFactory;

abstract class AbstractServiceAwareRuleTestCase extends RuleTestCase
{
    protected function getRuleFromConfig(string $ruleClass, string $config): Rule
    {
        $phpStanContainerFactory = new PHPStanContainerFactory();
        $container = $phpStanContainerFactory->createContainer([$config]);

        return $container->getByType($ruleClass);
    }
}
