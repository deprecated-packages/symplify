<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\PreventDuplicateClassMethodRuleTest
 */
final class PreventDuplicateClassMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Content of method "%s()" is duplicated with method "%s()" in "%s" class. Use unique content or abstract service instead';

    /**
     * @var string[]
     */
    private const PHPSTAN_GET_NODE_TYPE_METHODS = ['getNodeType', 'getNodeTypes'];

    /**
     * @var string[]
     */
    private const EXCLUDED_TYPES = [Kernel::class, Extension::class];

    /**
     * @var string
     * @see https://regex101.com/r/cJZZgC/1
     */
    private const VARIABLE_REGEX = '#\$\w+[^\s]#';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var Standard
     */
    private $printerStandard;

    /**
     * @var array<int, array<int, array<string, string>>>
     */
    private $contentMethodByCountParamName = [];

    public function __construct(SimpleNameResolver $simpleNameResolver, Standard $printerStandard)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->printerStandard = $printerStandard;
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
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return [];
        }

        if ($this->shouldSkip($node, $className)) {
            return [];
        }

        /** @var string $classMethodName */
        $classMethodName = $this->simpleNameResolver->getName($node);
        if (in_array($classMethodName, self::PHPSTAN_GET_NODE_TYPE_METHODS, true)) {
            return [];
        }

        /** @var Node[] $stmts */
        $stmts = (array) $node->stmts;
        $stmtCount = count($stmts);
        if ($stmtCount <= 1) {
            return [];
        }

        $printStmts = $this->getPrintStmts($node);
        $countParam = count($node->params);
        $this->contentMethodByCountParamName[$countParam] = $this->contentMethodByCountParamName[$countParam] ?? [];

        foreach ($this->contentMethodByCountParamName[$countParam] as $contentMethod) {
            if ($contentMethod['content'] === $printStmts) {
                return [
                    sprintf(self::ERROR_MESSAGE, $classMethodName, $contentMethod['method'], $contentMethod['class']),
                ];
            }
        }

        $this->contentMethodByCountParamName[$countParam][] = [
            'class' => $className,
            'method' => $classMethodName,
            'content' => $printStmts,
        ];

        return [];
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
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}

class B
{
    public function someMethod()
    {
        echo 'statement';
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
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}

class B
{
    public function someMethod()
    {
        echo 'statement';
        (new SmartFinder())->run('.js');
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkip(ClassMethod $classMethod, string $className): bool
    {
        if ($this->isExcludedTypes($className)) {
            return true;
        }

        if (interface_exists($className)) {
            return true;
        }

        if (trait_exists($className)) {
            return true;
        }

        return $this->isConstructorOrInTestClass($classMethod, $className);
    }

    private function getPrintStmts(ClassMethod $classMethod): string
    {
        $content = $this->printerStandard->prettyPrint((array) $classMethod->stmts);
        return Strings::replace($content, self::VARIABLE_REGEX, function (array $match): string {
            return '$a';
        });
    }

    private function isExcludedTypes(string $className): bool
    {
        foreach (self::EXCLUDED_TYPES as $excludedType) {
            if (is_a($className, $excludedType, true)) {
                return true;
            }
        }

        return false;
    }

    private function isConstructorOrInTestClass(ClassMethod $classMethod, string $className): bool
    {
        if ($this->simpleNameResolver->isName($classMethod->name, MethodName::CONSTRUCTOR)) {
            return true;
        }

        return Strings::endsWith($className, 'Test');
    }
}
