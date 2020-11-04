<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoMethodTagInClassDocblockRule\NoMethodTagInClassDocblockRuleTest
 */
final class NoMethodTagInClassDocblockRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use @method tag in class docblock';

    /**
     * @var string
     * @see https://regex101.com/r/lpeFd6/1
     */
    public const METHOD_TAG_REGEX = '#\*\s+@method\s+.*\n?#';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return [];
        }

        if (! Strings::match($docComment->getText(), self::METHOD_TAG_REGEX)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
/**
 * @method getMagic() string
 */
class SomeClass
{
    public function __call()
    {
        // more magic
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function getExplicitValue()
    {
        return 'explicit';
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
