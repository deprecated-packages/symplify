<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PrefferedMethodCallOverIdenticalCompareRule\PrefferedMethodCallOverIdenticalCompareRuleTest
 */
final class PrefferedMethodCallOverIdenticalCompareRule extends AbstractPrefferedCallOverFuncRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "%s->%s(\'value\')" method call over "%s() === \'value\'" comparison';

    /**
     * @var array<string, string[]>
     */
    private $identicalToPreferredMethodCalls = [];

    /**
     * @param array<string, string[]> $simpleNameResolver
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $identicalToPreferredMethodCalls = [])
    {
        parent::__construct($simpleNameResolver);

        $this->identicalToPreferredMethodCalls = $identicalToPreferredMethodCalls;
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
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
