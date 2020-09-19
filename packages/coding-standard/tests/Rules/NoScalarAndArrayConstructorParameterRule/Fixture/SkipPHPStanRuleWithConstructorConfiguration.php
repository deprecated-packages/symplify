<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

final class SkipPHPStanRuleWithConstructorConfiguration implements Rule
{
    /**
     * @var int
     */
    private $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    public function getNodeType(): string
    {
    }

    public function processNode(Node $node, Scope $scope): array
    {
    }
}
