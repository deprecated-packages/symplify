<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use MyCLabs\Enum\Enum;
use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoMethodTagInClassDocblockRule\NoMethodTagInClassDocblockRuleTest
 */
final class NoMethodTagInClassDocblockRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use @method tag in class docblock';

    /**
     * @var string
     * @see https://regex101.com/r/lpeFd6/1
     */
    private const METHOD_TAG_REGEX = '#\*\s+@method\s+.*\n?#';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return [];
        }

        if (! Strings::match($docComment->getText(), self::METHOD_TAG_REGEX)) {
            return [];
        }

        // enums are the only exception for annotation
        $classReflection = $node->getClassReflection();
        if ($classReflection->isSubclassOf(Enum::class)) {
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
