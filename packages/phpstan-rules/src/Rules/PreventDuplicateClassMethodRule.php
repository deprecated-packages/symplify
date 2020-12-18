<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\ConstExprEvaluator;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\SymfonyPhpConfigClosureAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Nette\Utils\Strings;
use Symplify\PHPStanRules\ValueObject\MethodName;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\PreventDuplicateClassMethodRuleTest
 */
final class PreventDuplicateClassMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Content of method "%s" is duplicated, use unique content instead';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var SymfonyPhpConfigClosureAnalyzer
     */
    private $symfonyPhpConfigClosureAnalyzer;

    /**
     * @var array<string, string[]>
     */
    private $contentMethodByNameFile = [];

    /**
     * @var ConstExprEvaluator
     */
    private $constExprEvaluator;

    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        SymfonyPhpConfigClosureAnalyzer $symfonyPhpConfigClosureAnalyzer,
        ConstExprEvaluator $constExprEvaluator
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->symfonyPhpConfigClosureAnalyzer = $symfonyPhpConfigClosureAnalyzer;
        $this->constExprEvaluator = $constExprEvaluator;
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
        if ($this->shouldSkip($node, $scope)) {
            return [];
        }

        return [];
    }

    private function shouldSkip(ClassMethod $classMethod, Scope $scope): bool
    {
        if ($scope->getClassReflection() === null) {
            return true;
        }

        if (! $this->simpleNameResolver->isName($classMethod->name, MethodName::CONSTRUCTOR)) {
            return true;
        }

        /** @var Class_|null $class */
        $class = $this->resolveCurrentClass($classMethod);

        if ($class === null || Strings::endWith($class->toString(), 'Test')) {
            return true;
        }

        return false;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class A
{
    public function someMethod()
    {
        (new SmartFinder())->run('.php');
    }
}

class B
{
    public function someMethod()
    {
        (new SmartFinder())->run('.php');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class A
{
    public function someMethod()
    {
        (new SmartFinder())->run('.php');
    }
}

class B
{
    public function someMethod()
    {
        (new SmartFinder())->run('.js');
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
