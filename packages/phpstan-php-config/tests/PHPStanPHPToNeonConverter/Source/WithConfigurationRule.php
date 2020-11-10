<?php
declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\Tests\PHPStanPHPToNeonConverter\Source;


final class WithConfigurationRule implements \PHPStan\Rules\Rule
{
    /**
     * @var string
     */
    private $someValue;

    public function __construct(string $someValue)
    {
        $this->someValue = $someValue;
    }

    public function getNodeType(): string
    {
    }

    public function processNode(\PhpParser\Node $node, \PHPStan\Analyser\Scope $scope): array
    {
    }
}
