<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules\ObjectCalisthenics;

use PhpParser\Node;
use PhpParser\Node\Stmt\Else_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#2-do-not-use-else-keyword
 */
final class NoElseRule implements Rule
{
    public function getNodeType(): string
    {
        return Else_::class;
    }

    /**
     * @param Else_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        return ['Do not use "else" keyword'];
    }
}
