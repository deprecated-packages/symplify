<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Testing;

use Nette\Utils\Strings;
use PHPStan\DependencyInjection\Container;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Comparator\ComparisonFailure;
use Symplify\PHPStanExtensions\DependencyInjection\PHPStanContainerFactory;
use Symplify\PHPStanExtensions\Exception\SwappedArgumentsException;

/**
 * @template TRule of \PHPStan\Rules\Rule
 * @template-extends RuleTestCase<TRule>
 */
abstract class AbstractServiceAwareRuleTestCase extends RuleTestCase
{
    /**
     * @var array<string, Container>
     */
    private static $containersByConfig = [];

    public function analyse(array $filePaths, array $expectedErrorsWithLines): void
    {
        try {
            parent::analyse($filePaths, $expectedErrorsWithLines);
        } catch (ExpectationFailedException $throwable) {
            if ($this->isMatchTokenEmulationException($throwable)) {
                return;
            }

            throw $throwable;
        }
    }

    /**
     * @param class-string<TRule> $ruleClass
     * @return TRule
     */
    protected function getRuleFromConfig(string $ruleClass, string $config): Rule
    {
        if (Strings::contains($config, '\\') && file_exists($ruleClass)) {
            $message = sprintf('Swapped arguments in "%s()" method', __METHOD__);
            throw new SwappedArgumentsException($message);
        }

        $container = $this->getServiceContainer($config);

        return $container->getByType($ruleClass);
    }

    /**
     * Fix for T_MATCH emulation type conflicts between php-parser and php_codesniffer
     * https://github.com/symplify/symplify/pull/3107#issuecomment-822251092
     */
    private function isMatchTokenEmulationException(ExpectationFailedException $expectationFailedException): bool
    {
        // already native T_MATCH token
        if (PHP_VERSION_ID >= 80000) {
            return false;
        }

        $comparisonFailure = $expectationFailedException->getComparisonFailure();
        if (! $comparisonFailure instanceof ComparisonFailure) {
            return false;
        }

        $actualAsString = $comparisonFailure->getActualAsString();

        return Strings::contains(
            $actualAsString,
            'Return value of PhpParser\Lexer\TokenEmulator\MatchTokenEmulator::getKeywordToken() must be of the type int, string returned'
        );
    }

    private function getServiceContainer(string $config): Container
    {
        if (isset(self::$containersByConfig[$config])) {
            return self::$containersByConfig[$config];
        }

        $phpStanContainerFactory = new PHPStanContainerFactory();
        $container = $phpStanContainerFactory->createContainer([$config]);
        self::$containersByConfig[$config] = $container;

        return $container;
    }
}
