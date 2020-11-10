<?php
declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\Tests\PHPStanPHPToNeonConverter\Source;


final class SimpleRule implements \PHPStan\Rules\Rule
{
    public function getNodeType(): string
    {
    }

    public function processNode(\PhpParser\Node $node, \PHPStan\Analyser\Scope $scope): array
    {
    }
}
