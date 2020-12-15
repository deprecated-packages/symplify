<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\RequireQuoteStringValueSprintfRuleTest
 */
final class RequireQuoteStringValueSprintfRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"%s" in sprintf() format must be quoted';

    /**
     * @return string[]
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
        if (! $node->name instanceof Name) {
            return [];
        }

        $funcName = $node->name->toString();
        if ($funcName !== 'sprintf') {
            return [];
        }

        $args = $node->args;
        if (count($args) === 1) {
            return [];
        }

        $format = $args[0]->value;
        if (! $format instanceof String_ || trim($format->value) === '') {
            return [];
        }

        $positionStringFormat = strpos($format->value, '%s');
        if ($positionStringFormat === false) {
            return [];
        }

        if ($positionStringFormat === 0 || $positionStringFormat === strlen($format->value) - 1) {
            return [self::ERROR_MESSAGE];
        }

        if (! $this->isQuoted($format->value, $positionStringFormat)) {
            return [self::ERROR_MESSAGE];
        }

        return [];
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
        echo sprintf('%s value', $variable);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        echo sprintf('"%s" value', $variable);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isQuoted(string $formatValue, int $positionStringFormat): bool
    {
        return substr($formatValue, $positionStringFormat - 1, 1) === '"'
            && substr($formatValue, $positionStringFormat + 2, 1) === '"';
    }
}
