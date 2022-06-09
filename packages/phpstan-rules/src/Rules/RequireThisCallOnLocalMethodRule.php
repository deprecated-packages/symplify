<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireThisCallOnLocalMethodRule\RequireThisCallOnLocalMethodRuleTest
 * @implements Rule<InClassNode>
 */
final class RequireThisCallOnLocalMethodRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "$this-><method>()" instead of "self::<method>()" to call local method';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private SimpleNodeFinder $simpleNodeFinder
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

        /** @var StaticCall[] $staticCalls */
        $staticCalls = $this->simpleNodeFinder->findByType($classLike, StaticCall::class);
        foreach ($staticCalls as $staticCall) {
            if (! $this->simpleNameResolver->isName($staticCall->class, 'self')) {
                continue;
            }

            $classMethod = $this->getClassMethodInCurrentClass($staticCall, $classLike);
            if (! $classMethod instanceof ClassMethod) {
                continue;
            }

            if ($classMethod->isStatic()) {
                continue;
            }

            $errorMessages[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($staticCall->getLine())
                ->build();
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        self::execute();
    }

    private function execute()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        $this->execute();
    }

    private function execute()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function getClassMethodInCurrentClass(StaticCall $staticCall, Class_ $class): ?ClassMethod
    {
        $staticCallName = $this->simpleNameResolver->getName($staticCall->name);
        if ($staticCallName === null) {
            return null;
        }

        return $class->getMethod($staticCallName);
    }
}
