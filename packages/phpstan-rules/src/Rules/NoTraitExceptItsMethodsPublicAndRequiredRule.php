<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoTraitExceptItsMethodsPublicAndRequiredRule\NoTraitExceptItsMethodsPublicAndRequiredRuleTest
 */
final class NoTraitExceptItsMethodsPublicAndRequiredRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use trait';

    /**
     * @var string
     * @see https://regex101.com/r/gn2P0C/1
     */
    private const REQUIRED_DOCBLOCK_REGEX = '#\*\s+@required\n?#';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Trait_::class];
    }

    /**
     * @param Trait_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $methods = $node->getMethods();
        if ($methods === []) {
            return [self::ERROR_MESSAGE];
        }

        foreach ($methods as $method) {
            $docComment = $method->getDocComment();
            if ($docComment === null || ! $method->isPublic()) {
                return [self::ERROR_MESSAGE];
            }

            if (! Strings::match($docComment->getText(), self::REQUIRED_DOCBLOCK_REGEX)) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }
}
