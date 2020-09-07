<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\TooLongVariableRule\TooLongVariableRuleTest
 */
final class TooLongVariableRule implements Rule
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

    public function getNodeType(): string
    {
        return Variable::class;
    }

    /**
     * @param Variable $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
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
