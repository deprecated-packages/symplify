<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\Rules\AbstractManyNodeTypeRule;

/**
 * @see \Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\TooLongFunctionLikeRule\TooLongFunctionLikeRuleTest
 */
final class TooLongFunctionLikeRule extends AbstractManyNodeTypeRule
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
