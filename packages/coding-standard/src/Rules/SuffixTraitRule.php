<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\SuffixTraitRule\SuffixTraitRuleTest
 */
final class SuffixTraitRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Trait name "%s" must be suffixed with "Trait"';

    public function getNodeType(): string
    {
        return Trait_::class;
    }

    /**
     * @param Trait_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $traitName = (string) $node->name;
        if (Strings::endsWith($traitName, 'Trait')) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $traitName)];
    }
}
