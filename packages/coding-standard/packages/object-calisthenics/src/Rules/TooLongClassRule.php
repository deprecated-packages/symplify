<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\TooLongClassRule\TooLongClassRuleTest
 */
final class TooLongClassRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '%s has %d lines, it is too long. Shorted it under %d lines';

    /**
     * @var int
     */
    private $maxClassLength;

    public function __construct(int $maxClassLength)
    {
        $this->maxClassLength = $maxClassLength;
    }

    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    /**
     * @param ClassLike $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $currentClassLenght = $this->getNodeLength($node);
        if ($currentClassLenght <= $this->maxClassLength) {
            return [];
        }

        $classLikeType = $this->resolveClassLikeType($node);

        $errorMessage = sprintf(self::ERROR_MESSAGE, $classLikeType, $currentClassLenght, $this->maxClassLength);
        return [$errorMessage];
    }

    private function resolveClassLikeType(ClassLike $classLike): string
    {
        if ($classLike instanceof Class_) {
            return 'Class';
        }

        if ($classLike instanceof Interface_) {
            return 'Interface';
        }

        return 'Trait';
    }

    private function getNodeLength(Node $node): int
    {
        return $node->getEndLine() - $node->getStartLine();
    }
}
