<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\TooLongClassLikeRule\TooLongClassLikeRuleTest
 */
final class TooLongClassLikeRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '%s has %d lines, it is too long. Shorted it under %d lines';

    /**
     * @var int
     */
    private $maxClassLikeLength;

    public function __construct(int $maxClassLikeLength = 300)
    {
        $this->maxClassLikeLength = $maxClassLikeLength;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassLike::class];
    }

    /**
     * @param ClassLike $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $currentClassLenght = $node->getEndLine() - $node->getStartLine();
        if ($currentClassLenght <= $this->maxClassLikeLength) {
            return [];
        }

        $classLikeType = $this->resolveClassLikeType($node);

        $errorMessage = sprintf(self::ERROR_MESSAGE, $classLikeType, $currentClassLenght, $this->maxClassLikeLength);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function someMethod()
    {
        if (...) {
            return 1;
        } else {
            return 2;
        }
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function someMethod()
    {
        return (...) ? 1 : 2;
    }
}
CODE_SAMPLE
                ,
                [
                    'maxClassLikeLength' => 3,
                ]
            ),
        ]);
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
}
