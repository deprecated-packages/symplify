<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\ObjectCalisthenics\Rules\NoChainMethodCallRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\ObjectCalisthenics\Rules\NoChainMethodCallRule\Source\InterfaceToMock;
use Twig\Source;

final class SkipMockFluent extends TestCase
{
    public function go(): void
    {
        $someMock = $this->createMock(InterfaceToMock::class);
        $someMock->expects($this->once())
            ->method('getSourceContext');
    }
}
