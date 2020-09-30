<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoConstructorInTestRule\NoConstructorInTestRuleTest
 */
final class NoConstructorInTestRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use constructor in test, only setUp()';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ((string) $node->name !== '__construct') {
            return [];
        }

        /** @var Class_ */
        $class = $node->getAttribute('parent');
        /** @var Identifier */
        $name = $class->name;
        $className = $name->toString();

        if (substr($className, -4) !== 'Test') {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
