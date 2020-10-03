<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoConstructorInTestRule\NoConstructorInTestRuleTest
 */
final class NoConstructorInTestRule extends AbstractSymplifyRule
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

        $className = $this->getClassName($scope);
        if ($className === null) {
            return [];
        }

        if (! Strings::endsWith($className, 'Test')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
