<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Finder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\PackageBuilder\Finder\SymfonyFileInfoFactory;

final class SymfonyFileInfoFactoryTest extends TestCase
{
    public function test(): void
    {
        $symfonyFileInfo = SymfonyFileInfoFactory::createFromFilePath(
            __DIR__ . '/SymfonyFileInfoFactorySource/SomeFile.txt'
        );

        $this->assertInstanceOf(SplFileInfo::class, $symfonyFileInfo);
    }
}
