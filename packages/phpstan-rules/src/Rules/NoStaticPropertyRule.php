<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PHPStan\Analyser\Scope;
use PHPStan\DependencyInjection\Container;
use PHPStan\Reflection\ClassReflection;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\PHPStanRules\TypeAnalyzer\ContainsTypeAnalyser;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoStaticPropertyRule\NoStaticPropertyRuleTest
 */
final class NoStaticPropertyRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use static property';

    /**
     * @var array<class-string>
     */
    private const ALLOWED_TYPES = [ContainerInterface::class, Container::class, KernelInterface::class];

    public function __construct(
        private ContainsTypeAnalyser $containsTypeAnalyser
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticPropertyFetch::class];
    }

    /**
     * @param StaticPropertyFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->isAbstractTestCase($scope)) {
            return [];
        }

        if ($this->containsTypeAnalyser->containsExprTypes($node, $scope, self::ALLOWED_TYPES)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private static $customFileNames = [];
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private $customFileNames = [];
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isAbstractTestCase(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }
        if (! $classReflection->isAbstract()) {
            return false;
        }

        return $classReflection->isSubclassOf(TestCase::class);
    }
}
