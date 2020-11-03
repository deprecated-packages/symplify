<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\TooLongVariableRule\TooLongVariableRuleTest
 */
final class TooLongVariableRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Variable "$%s" is too long with %d chars. Narrow it under %d chars';

    /**
     * @var int
     */
    private $maxVariableLength;

    public function __construct(int $maxVariableLength = 20)
    {
        $this->maxVariableLength = $maxVariableLength;
    }

    /**
     * @return string[]
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

        $variableName = (string) $node->name;
        $currentVariableLenght = Strings::length($variableName);

        if ($currentVariableLenght < $this->maxVariableLength) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $variableName, $currentVariableLenght, $this->maxVariableLength);
        return [$errorMessage];
    }
}
