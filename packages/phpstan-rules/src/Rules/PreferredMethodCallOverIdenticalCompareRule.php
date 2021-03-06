<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use Rector\Core\Rector\AbstractRector;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreferredMethodCallOverIdenticalCompareRule\PreferredMethodCallOverIdenticalCompareRuleTest
 */
final class PreferredMethodCallOverIdenticalCompareRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "%s->%s(\'value\')" method call over "%s->%s() === \'value\'" comparison';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var array<string, array<string, string>>
     */
    private $identicalToPreferredMethodCalls = [];

    /**
     * @param array<string, array<string, string>> $identicalToPreferredMethodCalls
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $identicalToPreferredMethodCalls = [])
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->identicalToPreferredMethodCalls = $identicalToPreferredMethodCalls;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Identical::class, NotIdentical::class];
    }

    /**
     * @param Identical|NotIdentical $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $left = $node->left;
        $right = $node->right;

        if (! $left instanceof MethodCall && ! $right instanceof MethodCall) {
            return [];
        }

        /** @var MethodCall $methodCall */
        $methodCall = $left instanceof MethodCall
            ? $left
            : $right;

        $type = $this->getMethodCallType($methodCall, $scope);
        if (! $type instanceof ObjectType) {
            return [];
        }

        return $this->validateIdenticalCompare($type, $methodCall);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        $this->getName($node) === 'hey';
        $this->getName($node) !== 'hey';
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\Utils\Strings;

class SomeClass
{
    public function run($value)
    {
        $this->isName($node, 'hey');
        ! $this->isName($node, 'hey');
    }
}
CODE_SAMPLE
                ,
                [
                    'identicalToPreferredMethodCalls' => [
                        AbstractRector::class => [
                            'getName' => 'isName',
                        ],
                    ],
                ]
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function validateIdenticalCompare(ObjectType $objectType, MethodCall $methodCall): array
    {
        $className = $objectType->getClassName();
        foreach ($this->identicalToPreferredMethodCalls as $class => $methodCalls) {
            if (! is_a($className, $class, true)) {
                continue;
            }

            foreach ($methodCalls as $old => $new) {
                if (! $this->simpleNameResolver->isName($methodCall->name, $old)) {
                    continue;
                }

                return [sprintf(self::ERROR_MESSAGE, $class, $new, $class, $old)];
            }
        }

        return [];
    }

    private function getMethodCallType(MethodCall $methodCall, Scope $scope): ?ObjectType
    {
        $type = $scope->getType($methodCall->var);
        if ($type instanceof ThisType) {
            $type = $type->getStaticObjectType();
        }

        if (! $type instanceof ObjectType) {
            return null;
        }

        return $type;
    }
}
