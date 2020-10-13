<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Comment\Doc;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoMethodTagInClassDocblockRule\NoMethodTagInClassDocblockRuleTest
 */
final class NoMethodTagInClassDocblockRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use @method tag in class docblock';

    /**
     * @var string
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
     * @param Comment $node
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
}
