<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequiredAbstractClassKeywordRule\RequiredAbstractClassKeywordRuleTest
 */
final class RequiredAbstractClassKeywordRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class name starting with "Abstract" must have an `abstract` keyword';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isAbstract()) {
            return [];
        }

        $shortClassName = $this->simpleNameResolver->resolveShortNameFromNode($node);
        if ($shortClassName === null) {
            return [];
        }

        if (! \str_starts_with($shortClassName, 'Abstract')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class AbstractClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
abstract class AbstractClass
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
