<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\Rule;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PHPStanRules\Enum\MethodName;
use Symplify\PHPStanRules\Printer\DuplicatedClassMethodPrinter;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\PreventDuplicateClassMethodRuleTest
 */
final class PreventDuplicateClassMethodRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Content of method "%s()" is duplicated with method "%s()" in "%s" class. Use unique content or service instead';

    /**
     * @var string[]
     */
    private const PHPSTAN_GET_NODE_TYPE_METHODS = ['getNodeType', 'getNodeTypes'];

    /**
     * @var array<class-string>
     */
    private const EXCLUDED_TYPES = [Kernel::class, Extension::class];

    /**
     * @var array<array<string, string>>
     */
    private array $classMethodContents = [];

    public function __construct(
        private DuplicatedClassMethodPrinter $duplicatedClassMethodPrinter,
        private int $minimumLineCount = 3
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    /**
     * @param InClassMethodNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $methodReflection = $node->getMethodReflection();
        $declaringClassReflection = $methodReflection->getDeclaringClass();

        $classMethod = $node->getOriginalNode();

        $className = $declaringClassReflection->getName();
        if ($this->shouldSkip($classMethod, $scope, $className)) {
            return [];
        }

        $classMethodName = $classMethod->name->toString();
        if (in_array($classMethodName, self::PHPSTAN_GET_NODE_TYPE_METHODS, true)) {
            return [];
        }

        $printStmts = $this->duplicatedClassMethodPrinter->printClassMethod($classMethod);

        $validateDuplication = $this->validateDuplication($classMethodName, $printStmts);
        if ($validateDuplication !== []) {
            return $validateDuplication;
        }

        $this->classMethodContents[] = [
            'class' => $className,
            'method' => $classMethodName,
            'content' => $printStmts,
        ];

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function someMethod()
    {
        echo 'statement';
        $value = new SmartFinder();
    }
}

class AnotherClass
{
    public function someMethod()
    {
        echo 'statement';
        $differentValue = new SmartFinder();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function someMethod()
    {
        echo 'statement';
        $value = new SmartFinder();
    }
}
}
CODE_SAMPLE
                ,
                [
                    'minimumLineCount' => 3,
                ]
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function validateDuplication(string $classMethodName, string $currentClassMethodContent): array
    {
        foreach ($this->classMethodContents as $classMethodContent) {
            if ($classMethodContent['content'] !== $currentClassMethodContent) {
                continue;
            }

            $errorMessage = sprintf(
                self::ERROR_MESSAGE,
                $classMethodName,
                $classMethodContent['method'],
                $classMethodContent['class']
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

        foreach (self::EXCLUDED_TYPES as $excludedType) {
            if (is_a($className, $excludedType, true)) {
                return true;
            }
        }

        /** @var Stmt[] $stmts */
        $stmts = (array) $classMethod->stmts;
        if (count($stmts) < $this->minimumLineCount) {
            return true;
        }

        return $this->isConstructorOrInTestClass($classMethod, $className);
    }

    private function isConstructorOrInTestClass(ClassMethod $classMethod, string $className): bool
    {
        if ($classMethod->name->toString() === MethodName::CONSTRUCTOR) {
            return true;
        }

        return \str_ends_with($className, 'Test');
    }
}
