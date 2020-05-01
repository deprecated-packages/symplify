<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\PHPStan\Testing;

use PHPStan\DependencyInjection\ContainerFactory;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

abstract class AbstractServiceAwareRuleTestCase extends RuleTestCase
{
    protected function getRuleFromConfig(string $ruleClass, string $config): Rule
    {
        $containerFactory = new ContainerFactory(getcwd());

        $tempDirectory = sys_get_temp_dir() . '/_symplify_coding_standar_phpstan_factory_temp';
        $container = $containerFactory->create($tempDirectory, [$config], [], []);

        return $container->getByType($ruleClass);
    }
}
