<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Config;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Webmozart\Assert\Assert;

/**
 * @api
 */
final class ECSConfig extends ContainerConfigurator
{
    /**
     * @param string[] $paths
     */
    public function paths(array $paths): void
    {
        Assert::allString($paths);

        $parameters = $this->parameters();
        $parameters->set(Option::PATHS, $paths);
    }

    /**
     * @param mixed[] $skips
     */
    public function skip(array $skips): void
    {
        $parameters = $this->parameters();
        $parameters->set(Option::SKIP, $skips);
    }

    /**
     * @param string[] $sets
     */
    public function sets(array $sets): void
    {
        Assert::allString($sets);
        Assert::allFileExists($sets);

        foreach ($sets as $set) {
            $this->import($set);
        }
    }

    /**
     * @param class-string<Sniff|FixerInterface> $checkerClass
     * @param array<string, mixed> $configuration See: https://mlocati.github.io/php-cs-fixer-configurator/
     */
    public function rule(string $checkerClass, ?array $configuration = []): void
    {
        $this->isCheckerClass($checkerClass);
        $service = $this->services()->set($checkerClass);

        if (!empty($configuration) && is_a($checkerClass, FixerInterface::class, true)) {
            Assert::isAnyOf($checkerClass, [ConfigurableFixerInterface::class, ConfigurableRuleInterface::class]);

            $service->call('configure', [$configuration]);
        }
    }

    /**
     * @param class-string $checkerClass
     * @param mixed[] $configuration
     * @deprecated
     */
    public function ruleWithConfiguration(string $checkerClass, array $configuration): void
    {
        $this->rule($checkerClass, $configuration);
    }

    /**
     * @param class-string $checkerClass
     */
    private function isCheckerClass(string $checkerClass): void
    {
        Assert::isAnyOf($checkerClass, [Sniff::class, FixerInterface::class]);
    }
}
