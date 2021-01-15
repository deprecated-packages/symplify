<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
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
    public const ERROR_MESSAGE = 'Use "%s(\'value\')" method call over "%s() === \'value\'" comparison';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var array<string, string[]>
     */
    private $identicalToPreferredMethodCalls = [];

    /**
     * @param array<string, string[]> $simpleNameResolver
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $identicalToPreferredMethodCalls = [])
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->identicalToPreferredMethodCalls = $identicalToPreferredMethodCalls;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Identical::class];
    }

    /**
     * @param Identical $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $left = $node->left;
        $right = $node->right;

        if (! $left instanceof MethodCall && ! $right instanceof MethodCall) {
            return [];
        }

        return [self::ERROR_MESSAGE];
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
    }
}
CODE_SAMPLE
                ,
                [
                    'identicalToPreferredMethodCalls' => [
                        'Rector\Core\Rector\AbstractRector' => [
                            'getName' => 'isName',
                        ],
                    ],
                ]
            ),
        ]);
    }
}
