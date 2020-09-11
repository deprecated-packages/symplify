<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Testing;

use Nette\Utils\Strings;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanExtensions\DependencyInjection\PHPStanContainerFactory;
use Symplify\PHPStanExtensions\Exception\SwappedArgumentsException;

abstract class AbstractServiceAwareRuleTestCase extends RuleTestCase
{
    protected function getRuleFromConfig(string $ruleClass, string $config): Rule
    {
        if (Strings::contains($config, '\\') && file_exists($ruleClass)) {
            $message = sprintf('Swapped arguments in "%s()" method', __METHOD__);
            throw new SwappedArgumentsException($message);
        }

        $phpStanContainerFactory = new PHPStanContainerFactory();
        $container = $phpStanContainerFactory->createContainer([$config]);

        return $container->getByType($ruleClass);
    }
}
