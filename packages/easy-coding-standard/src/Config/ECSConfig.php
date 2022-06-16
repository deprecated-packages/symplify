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
use Webmozart\Assert\InvalidArgumentException;

/**
 * @api
 */
final class ECSConfig extends ContainerConfigurator
{
    /**
     * @param string[] $paths
     */
    public function paths(array $paths): self
    {
        Assert::allString($paths);

        $parameters = $this->parameters();
        $parameters->set(Option::PATHS, $paths);

        return $this;
    }

    /**
     * @param mixed[] $skips
     */
    public function skip(array $skips): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::SKIP, $skips);

        return $this;
    }

    /**
     * @param mixed[] $onlys
     */
    public function only(array $onlys): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::ONLY, $onlys);

        return $this;
    }

    /**
     * @param string[] $sets
     */
    public function sets(array $sets): self
    {
        Assert::allString($sets);
        Assert::allFileExists($sets);

        foreach ($sets as $set) {
            $this->import($set);
        }

        return $this;
    }

    /**
     * @param class-string<Sniff|FixerInterface> $checkerClass
     */
    public function rule(string $checkerClass): self
    {
        $this->isCheckerClass($checkerClass);

        $services = $this->services();
        $services->set($checkerClass)
            ->public();

        return $this;
    }

    /**
     * @param array<class-string<Sniff|FixerInterface>> $checkerClasses
     */
    public function rules(array $checkerClasses): self
    {
        $this->ensureCheckerClassesAreUnique($checkerClasses);

        foreach ($checkerClasses as $checkerClass) {
            $this->rule($checkerClass);
        }

        return $this;
    }

    /**
     * @param class-string $checkerClass
     * @param mixed[] $configuration
     */
    public function ruleWithConfiguration(string $checkerClass, array $configuration): self
    {
        $this->isCheckerClass($checkerClass);

        $services = $this->services();

        $service = $services->set($checkerClass);
        if (is_a($checkerClass, FixerInterface::class, true)) {
            Assert::isAnyOf($checkerClass, [ConfigurableFixerInterface::class, ConfigurableRuleInterface::class]);

            $service->call('configure', [$configuration]);
        }

        if (is_a($checkerClass, Sniff::class, true)) {
            foreach ($configuration as $propertyName => $value) {
                Assert::propertyExists($checkerClass, $propertyName);

                $service->property($propertyName, $value);
            }
        }

        return $this;
    }

    /**
     * @param Option::INDENTATION_* $indentation
     */
    public function indentation(string $indentation): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::INDENTATION, $indentation);

        return $this;
    }

    public function lineEnding(string $lineEnding): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::LINE_ENDING, $lineEnding);

        return $this;
    }

    public function cacheDirectory(string $cacheDirectory): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::CACHE_DIRECTORY, $cacheDirectory);

        return $this;
    }

    public function cacheNamespace(string $cacheNamespace): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::CACHE_NAMESPACE, $cacheNamespace);

        return $this;
    }

    /**
     * @param string[] $fileExtensions
     */
    public function fileExtensions(array $fileExtensions): self
    {
        Assert::allString($fileExtensions);

        $parameters = $this->parameters();
        $parameters->set(Option::FILE_EXTENSIONS, $fileExtensions);

        return $this;
    }

    public function parallel(): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::PARALLEL, true);

        return $this;
    }

    public function parallelJobSize(int $parallelJobSize): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::PARALLEL_JOB_SIZE, $parallelJobSize);

        return $this;
    }

    public function parallelPort(string $parallelPort): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::PARALLEL_PORT, $parallelPort);

        return $this;
    }

    public function parallelIdentifier(string $parallelIdentifier): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::PARALLEL_IDENTIFIER, $parallelIdentifier);

        return $this;
    }

    public function parallelMaxNumberOfProcesses(int $parallelMaxNumberOfProcesses): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::PARALLEL_MAX_NUMBER_OF_PROCESSES, $parallelMaxNumberOfProcesses);

        return $this;
    }

    public function parallelTimeoutInSeconds(int $parallelTimeoutInSeconds): self
    {
        $parameters = $this->parameters();
        $parameters->set(Option::PARALLEL_TIMEOUT_IN_SECONDS, $parallelTimeoutInSeconds);

        return $this;
    }

    /**
     * @param class-string $checkerClass
     */
    private function isCheckerClass(string $checkerClass): void
    {
        Assert::classExists($checkerClass);
        Assert::isAnyOf($checkerClass, [Sniff::class, FixerInterface::class]);
    }

    /**
     * @param string[] $checkerClasses
     */
    private function ensureCheckerClassesAreUnique(array $checkerClasses): void
    {
        // ensure all rules are registered exactly once
        $checkerClassToCount = array_count_values($checkerClasses);
        $duplicatedCheckerClassToCount = array_filter($checkerClassToCount, fn (int $count): bool => $count > 1);

        if ($duplicatedCheckerClassToCount === []) {
            return;
        }

        $duplicatedCheckerClasses = array_flip($duplicatedCheckerClassToCount);

        $errorMessage = sprintf(
            'There are duplicated classes in $rectorConfig->rules(): "%s". Make them unique to avoid unexpected behavior.',
            implode('", "', $duplicatedCheckerClasses)
        );
        throw new InvalidArgumentException($errorMessage);
    }
}
