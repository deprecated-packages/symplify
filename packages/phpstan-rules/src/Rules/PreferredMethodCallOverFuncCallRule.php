<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\NodeAnalyzer\FuncCallMatcher;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreferredMethodCallOverFuncCallRule\PreferredMethodCallOverFuncCallRuleTest
 */
final class PreferredMethodCallOverFuncCallRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "%s->%s()" method call over "%s()" func call';

    /**
     * @var array<string, string[]>
     */
    private array $funcCallToPreferredMethodCalls = [];

    /**
     * @param array<string, string[]> $funcCallToPreferredMethodCalls
     */
    public function __construct(
        private FuncCallMatcher $funcCallMatcher,
        array $funcCallToPreferredMethodCalls = []
    ) {
        $this->funcCallToPreferredMethodCalls = $funcCallToPreferredMethodCalls;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        foreach ($this->funcCallToPreferredMethodCalls as $functionName => $methodCall) {
            if (! $this->funcCallMatcher->isFuncCallToCallMatch($node, $scope, $functionName, $methodCall)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $methodCall[0], $methodCall[1], $functionName);
            return [$errorMessage];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run($value)
    {
        return strlen($value);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\Utils\Strings;

class SomeClass
{
    public function __construct(Strings $strings)
    {
        $this->strings = $strings;
    }

    public function run($value)
    {
        return $this->strings->length($value);
    }
}
CODE_SAMPLE
                ,
                [
                    'funcCallToPreferredMethodCalls' => [
                        'strlen' => [Strings::class, 'length'],
                    ],
                ]
            ),
        ]);
    }
}
