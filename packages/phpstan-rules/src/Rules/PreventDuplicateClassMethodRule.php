<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Php\TypeChecker;
use Symplify\PHPStanRules\Printer\DuplicatedClassMethodPrinter;
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
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var array<array<string, string>>
     */
    private $classMethodContent = [];

    /**
     * @var string[]
     */
    private $reportedClassWithMethodDuplicate = [];

    /**
     * @var DuplicatedClassMethodPrinter
     */
    private $duplicatedClassMethodPrinter;

    /**
     * @var TypeChecker
     */
    private $typeChecker;

    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        DuplicatedClassMethodPrinter $duplicatedClassMethodPrinter,
        TypeChecker $typeChecker
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->duplicatedClassMethodPrinter = $duplicatedClassMethodPrinter;
        $this->typeChecker = $typeChecker;
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

        if ($this->shouldSkip($node, $scope, $className)) {
            return [];
        }

        /** @var string $classMethodName */
        $classMethodName = $this->simpleNameResolver->getName($node);
        if (in_array($classMethodName, self::PHPSTAN_GET_NODE_TYPE_METHODS, true)) {
            return [];
        }

        $printStmts = $this->duplicatedClassMethodPrinter->printClassMethod($node);

        $validateDuplication = $this->validateDuplication($className, $classMethodName, $printStmts);
        if ($validateDuplication !== []) {
            return $validateDuplication;
        }

        $this->classMethodContent[] = [
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

    /**
     * @return string[]
     */
    private function validateDuplication(
        string $className,
        string $classMethodName,
        string $currentPrintedClassMethod
    ): array {
        $duplicationPlaceholder = $className . $classMethodName;

        foreach ($this->classMethodContent as $contentMethod) {
            if ($contentMethod['content'] !== $currentPrintedClassMethod) {
                continue;
            }

            if (in_array($duplicationPlaceholder, $this->reportedClassWithMethodDuplicate, true)) {
                continue;
            }

            $this->reportedClassWithMethodDuplicate[] = $duplicationPlaceholder;
            $errorMessage = sprintf(
                self::ERROR_MESSAGE,
                $classMethodName,
                $contentMethod['method'],
                $contentMethod['class']
            );

            return [$errorMessage];
        }

        return [];
    }

    private function shouldSkip(ClassMethod $classMethod, Scope $scope, string $className): bool
    {
        if ($scope->isInTrait()) {
            return true;
        }

        if (! $scope->isInClass()) {
            return true;
        }

        if ($this->typeChecker->isInstanceOf($className, self::EXCLUDED_TYPES)) {
            return true;
        }

        /** @var Stmt[] $stmts */
        $stmts = (array) $classMethod->stmts;
        if (count($stmts) <= 1) {
            return true;
        }

        return $this->isConstructorOrInTestClass($classMethod, $className);
    }

    private function isConstructorOrInTestClass(ClassMethod $classMethod, string $className): bool
    {
        if ($this->simpleNameResolver->isName($classMethod->name, MethodName::CONSTRUCTOR)) {
            return true;
        }

        return Strings::endsWith($className, 'Test');
    }
}
