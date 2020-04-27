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
use PHPStan\Rules\Rule;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#6-do-not-abbreviate
 */
final class NoShortNameRule implements Rule
{
    /**
     * @var class-string[]
     */
    private const NODE_TYPES_WITH_NAME = [
        ClassLike::class,
        FunctionLike::class,
        ClassConst::class,
        PropertyProperty::class,
    ];

    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($node)) {
            return [];
        }

        $name = (string) $node->name;
        if (Strings::length($name) >= 3) {
            return [];
        }

        return ['Do not use names shorter than 3 chars'];
    }

    private function shouldSkip(Node $node): bool
    {
        foreach (self::NODE_TYPES_WITH_NAME as $nodeTypeWithName) {
            if (is_a($node, $nodeTypeWithName, true)) {
                return false;
            }
        }

        return true;
    }
}
