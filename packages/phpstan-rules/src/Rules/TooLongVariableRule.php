<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\TooLongVariableRule\TooLongVariableRuleTest
 */
final class TooLongVariableRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Variable "$%s" is too long with %d chars. Narrow it under %d chars';

    /**
     * @var int
     */
    private $maxVariableLength;

    public function __construct(int $maxVariableLength = 40)
    {
        $this->maxVariableLength = $maxVariableLength;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Variable::class];
    }

    /**
     * @param Variable $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->name instanceof Expr) {
            return [];
        }

        $variableName = $node->name;
        $currentVariableLength = Strings::length($variableName);

        if ($currentVariableLength < $this->maxVariableLength) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $variableName, $currentVariableLength, $this->maxVariableLength);
        return [$errorMessage];
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
        return $superLongVariableName;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return $shortName;
    }
}
CODE_SAMPLE
                ,
                [
                    'maxVariableLength' => 10,
                ]
            ),
        ]);
    }
}
