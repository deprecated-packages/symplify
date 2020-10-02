<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\ThisType;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PreferredRawDataInTestDataProvider\PreferredRawDataInTestDataProviderTest
 */
final class PreferredRawDataInTestDataProvider implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use raw data in test\'s dataProvider method';

    /**
     * @var string
     * @see https://regex101.com/r/WaNbZ1/1
     */
    private const DATAPROVIDER_REGEX = '#\*\s+@dataProvider\s+.*\n?#';

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

    }
}
