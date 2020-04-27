<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules\ObjectCalisthenics;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\PropertyProperty;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\Rules\AbstractManyNodeTypeRule;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#6-do-not-abbreviate
 */
final class NoShortNameRule extends AbstractManyNodeTypeRule
{
    /**
     * @return class-string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassLike::class, FunctionLike::class, ClassConst::class, PropertyProperty::class];
    }

    /**
     * @param ClassLike|FunctionLike|ClassConst|PropertyProperty $node
     */
    public function process(Node $node, Scope $scope): array
    {
        $name = (string) $node->name;
        if (Strings::length($name) >= 3) {
            return [];
        }

        return ['Do not use names shorter than 3 chars'];
    }
}
