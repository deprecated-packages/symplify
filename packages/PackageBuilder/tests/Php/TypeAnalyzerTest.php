<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Php;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Php\TypeAnalyzer;

final class TypeAnalyzerTest extends TestCase
{
    public function test(): void
    {
        $typeAnalyzer = new TypeAnalyzer();

        $this->assertTrue($typeAnalyzer->isPhpReservedType('string'));
    }
}
