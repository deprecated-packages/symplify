<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireDataProviderTestMethodRule\RequireDataProviderTestMethodRuleTest
 */
final class RequireDataProviderTestMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Test method "%s()" must use data provider';

    /**
     * @var string[]
     */
    private $classesRequiringDataProvider = [];

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @param string[] $classesRequiringDataProvider
     */
    public function __construct(
        ArrayStringAndFnMatcher $arrayStringAndFnMatcher,
        array $classesRequiringDataProvider = []
    ) {
        $this->classesRequiringDataProvider = $classesRequiringDataProvider;
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $methodName = (string) $node->name;
        if (! Strings::startsWith($methodName, 'test')) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        $className = $classReflection->getName();
        if (! $this->arrayStringAndFnMatcher->isMatchOrSubType($className, $this->classesRequiringDataProvider)) {
            return [];
        }

        if (count((array) $node->params) !== 0) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $methodName);
        return [$errorMessage];
    }
}
