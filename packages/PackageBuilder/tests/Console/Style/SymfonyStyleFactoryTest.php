<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Console\Style;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

final class SymfonyStyleFactoryTest extends TestCase
{
    public function test(): void
    {
        $symfonyStyle = SymfonyStyleFactory::create();
        $this->assertInstanceOf(SymfonyStyle::class, $symfonyStyle);
    }
}
