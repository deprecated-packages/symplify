<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\PHPStan\Testing;

use PHPStan\DependencyInjection\Container;
use PHPStan\DependencyInjection\ContainerFactory;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

abstract class AbstractServiceAwareRuleTestCase extends RuleTestCase
{
    protected function getRuleFromConfig(string $ruleClass, string $config): Rule
    {
        $container = $this->createContainer([$config]);
        return $container->getByType($ruleClass);
    }

    /**
     * @param string[] $configs
     */
    private function createContainer(array $configs): Container
    {
        $containerFactory = new ContainerFactory(getcwd());
        // random for tests cache invalidation in case the container changes
        $tempDirectory = sys_get_temp_dir() . '/_symplify_coding_standard_phpstan_factory_temp/id_' . random_int(
            0,
            1000
        );

        return $containerFactory->create($tempDirectory, $configs, [], []);
    }
}
