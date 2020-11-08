<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreferredStaticCallOverFuncCallRule\PreferredStaticCallOverFuncCallRuleTest
 */
final class PreferredStaticCallOverFuncCallRule extends AbstractPrefferedCallOverFuncRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "%s::%s()" static call over "%s()" func call';

    /**
     * @var array<string, string[]>
     */
    private $funcCallToPreferredStaticCalls = [];

    /**
     * @param array<string, string[]> $funcCallToPreferredStaticCalls
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $funcCallToPreferredStaticCalls = [])
    {
        parent::__construct($simpleNameResolver);

        $this->funcCallToPreferredStaticCalls = $funcCallToPreferredStaticCalls;
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        foreach ($this->funcCallToPreferredStaticCalls as $functionName => $staticCall) {
            if (! $this->isFuncCallToCallMatch($node, $scope, $functionName, $staticCall)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $staticCall[0], $staticCall[1], $functionName);
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
    public function run($value)
    {
        return Strings::lenght($value);
    }
}
CODE_SAMPLE
                ,
                [
                    'funcCallToPreferredStaticCalls' => [
                        'strlen' => [Strings::class, 'lenght'],
                    ],
                ]
            ),
        ]);
    }
}
