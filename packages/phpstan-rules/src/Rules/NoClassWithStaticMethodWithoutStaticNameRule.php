<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Matcher\ArrayStringAndFnMatcher;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule\NoClassWithStaticMethodWithoutStaticNameRuleTest
 */
final class NoClassWithStaticMethodWithoutStaticNameRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class has a static method must so must contains "Static" in its name';

    /**
     * @var array<class-string>
     */
    private const ALLOWED_CLASS_TYPES = [
        // symfony classes with static methods
        'Symfony\Component\EventDispatcher\EventSubscriberInterface',
        'Symfony\Component\Console\Command\Command',
    ];

    public function __construct(
        private NodeFinder $nodeFinder,
        private ArrayStringAndFnMatcher $arrayStringAndFnMatcher
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        if (! $classLike->name instanceof Identifier) {
            return [];
        }

        if (! $this->isClassWithStaticMethod($classLike)) {
            return [];
        }

        // skip anonymous class
        $shortClassName = $classLike->name->toString();
        if ($shortClassName === '') {
            return [];
        }

        // already has "Static" in the name
        if (\str_contains($shortClassName, 'Static')) {
            return [];
        }

        if ($this->shouldSkipClassName($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public static function getSome()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeStaticClass
{
    public static function getSome()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isClassWithStaticMethod(Class_ $class): bool
    {
        $classMethods = $class->getMethods();

        foreach ($classMethods as $classMethod) {
            if (! $classMethod->isStatic()) {
                continue;
            }

            if ($this->isStaticConstructorOfValueObject($classMethod)) {
                continue;
            }

            return true;
        }

        return false;
    }

    private function shouldSkipClassName(InClassNode $inClassNode): bool
    {
        $classReflection = $inClassNode->getClassReflection();

        return $this->arrayStringAndFnMatcher->isMatchWithIsA(
            $classReflection->getName(),
            self::ALLOWED_CLASS_TYPES
        );
    }

    private function isStaticConstructorOfValueObject(ClassMethod $classMethod): bool
    {
        return (bool) $this->nodeFinder->findFirst((array) $classMethod->stmts, static function (Node $node): bool {
            if (! $node instanceof Return_) {
                return false;
            }

            $returnedExpr = $node->expr;
            if (! $returnedExpr instanceof New_) {
                return false;
            }

            if (! $returnedExpr->class instanceof Name) {
                return false;
            }

            return $returnedExpr->class->toString() === 'self';
        });
    }
}
