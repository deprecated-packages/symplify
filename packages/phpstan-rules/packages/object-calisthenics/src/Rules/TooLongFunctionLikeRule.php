<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\TooLongFunctionLikeRule\TooLongFunctionLikeRuleTest
 */
final class TooLongFunctionLikeRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '%s has %d lines, it is too long. Shorted it under %d lines';

    /**
     * @var int
     */
    private $maxFunctionLikeLength;

    public function __construct(int $maxFunctionLikeLength)
    {
        $this->maxFunctionLikeLength = $maxFunctionLikeLength;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Function_::class, ClassMethod::class];
    }

    /**
     * @param Function_|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $currentFunctionLikeLenght = $this->getNodeLength($node);
        if ($currentFunctionLikeLenght <= $this->maxFunctionLikeLength) {
            return [];
        }

        $functionLikeType = $this->resolveFunctionLikeType($node);

        $errorMessage = sprintf(
            self::ERROR_MESSAGE,
            $functionLikeType,
            $currentFunctionLikeLenght,
            $this->maxFunctionLikeLength
        );

        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
function some()
{
    if (...) {
        return 1;
    } else {
        return 2;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
function some()
{
    return (...) ? 1 : 2;
}
CODE_SAMPLE
                ,
                [
                    'maxFunctionLikeLength' => 3,
                ]
            ),
        ]);
    }

    /**
     * @param Function_|ClassMethod $functionLike
     */
    private function resolveFunctionLikeType(FunctionLike $functionLike): string
    {
        if ($functionLike instanceof Function_) {
            return 'Function';
        }

        return 'Method';
    }

    private function getNodeLength(Node $node): int
    {
        return $node->getEndLine() - $node->getStartLine();
    }
}
