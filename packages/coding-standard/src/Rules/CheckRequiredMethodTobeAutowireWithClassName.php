<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckRequireMethodTobeAutowireWithClassName\CheckRequireMethodTobeAutowireWithClassNameTest
 */
final class CheckRequiredMethodTobeAutowireWithClassName implements Rule
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Method with @required need to be named autowire+class name';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        return [];
    }
}
