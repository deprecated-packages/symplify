<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Type\UnionType;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Reflection\StaticCallNodeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\NoDynamicPropertyOnStaticCallRuleTest
 */
final class NoDynamicPropertyOnStaticCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use non-dynamic property on static calls or class const fetches';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var StaticCallNodeAnalyzer
     */
    private $staticCallNodeAnalyzer;

    public function __construct(StaticCallNodeAnalyzer $staticCallNodeAnalyzer, SimpleNameResolver $simpleNameResolver)
    {
        $this->staticCallNodeAnalyzer = $staticCallNodeAnalyzer;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class, ClassConstFetch::class];
    }

    /**
     * @param StaticCall|ClassConstFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->class instanceof Name) {
            return [];
        }

        if ($this->staticCallNodeAnalyzer->isAbstractMethodStaticCall($node, $scope)) {
            return [];
        }

        if ($node->name instanceof Identifier && $this->simpleNameResolver->isName($node->name, 'class')) {
            return [];
        }

        $callerType = $scope->getType($node->class);
        if ($callerType instanceof UnionType) {
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
    public function run()
    {
        return $this->connection::literal();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return Connection::literal();
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
