<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireThisOnParentMethodCallRule\RequireThisOnParentMethodCallRuleTest
 * @implements Rule<InClassNode>
 */
final class RequireThisOnParentMethodCallRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "$this-><method>()" instead of "parent::<method>()" unless in the same named method';

    public function __construct(
        private NodeFinder $nodeFinder
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
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        $errorMessages = [];

        foreach ($classLike->getMethods() as $classMethod) {
            /** @var StaticCall[] $staticCalls */
            $staticCalls = $this->nodeFinder->findInstanceOf($classMethod, StaticCall::class);

            foreach ($staticCalls as $staticCall) {
                if ($this->isParentCallInSameClassMethod($staticCall, $classMethod)) {
                    continue;
                }

                if (! $staticCall->name instanceof Identifier) {
                    continue;
                }

                $staticCallMethodName = $staticCall->name->toString();
                if ($this->doesMethodExistsInCurrentClass($classLike, $staticCallMethodName)) {
                    continue;
                }

                $errorMessages[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                    ->line($staticCall->getLine())
                    ->build();
            }
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass extends SomeParentClass
{
    public function go()
    {
        parent::run();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass extends SomeParentClass
{
    public function go()
    {
        $this->run();
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function doesMethodExistsInCurrentClass(Class_ $class, string $methodName): bool
    {
        $classMethod = $class->getMethod($methodName);
        return $classMethod instanceof ClassMethod;
    }

    private function isParentCallInSameClassMethod(StaticCall $staticCall, ClassMethod $classMethod): bool
    {
        if (! $staticCall->class instanceof Name) {
            return false;
        }

        if ($staticCall->class->toString() !== 'parent') {
            return true;
        }

        if (! $staticCall->name instanceof Identifier) {
            return false;
        }

        return $classMethod->name->toString() === $staticCall->name->toString();
    }
}
