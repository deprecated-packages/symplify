<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\PropertyProperty;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\Rules\AbstractManyNodeTypeRule;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#6-do-not-abbreviate
 *
 * @see \Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoShortNameRule\NoShortNameRuleTest
 */
final class NoShortNameRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not name "%s", shorter than 3 chars';

    /**
     * @return class-string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassLike::class, Function_::class, ClassMethod::class, Const_::class, PropertyProperty::class];
    }

    /**
     * @param ClassLike|Function_|ClassMethod|Const_|PropertyProperty $node
     */
    public function process(Node $node, Scope $scope): array
    {
        $name = (string) $node->name;
        if (Strings::length($name) >= 3) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $name)];
    }
}
