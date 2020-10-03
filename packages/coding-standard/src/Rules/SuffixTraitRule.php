<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\SuffixTraitRule\SuffixTraitRuleTest
 */
final class SuffixTraitRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Trait name "%s" must be suffixed with "Trait"';

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
        $traitName = (string) $node->name;
        if (Strings::endsWith($traitName, 'Trait')) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $traitName)];
    }
}
