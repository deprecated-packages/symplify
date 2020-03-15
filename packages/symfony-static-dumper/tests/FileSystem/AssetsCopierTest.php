<?php

declare(strict_types=1);


namespace Symplify\SymfonyStaticDumper\Tests\FileSystem;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SymfonyStaticDumper\Application\SymfonyStaticDumperApplication;
use Symplify\SymfonyStaticDumper\FileSystem\AssetsCopier;
use Symplify\SymfonyStaticDumper\Routing\RoutesProvider;
use Symplify\SymfonyStaticDumper\Tests\TestProject\HttpKernel\TestSymfonyStaticDumperKernel;

final class AssetsCopierTest extends AbstractKernelTestCase
{
    /**
     * @var AssetsCopier
     */
    private $assetsCopier;

    protected function setUp(): void
    {
        $this->bootKernel(TestSymfonyStaticDumperKernel::class);

        $this->assetsCopier = self::$container->get(AssetsCopier::class);
    }

    public function test(): void
    {
        $this->assetsCopier->copyAssets(__DIR__ . '/Fixture', __DIR__ . '/temp');

        $this->assertFileExists(__DIR__ . '/temp/css/style.css');
        $this->assertFileNotExists(__DIR__ . '/temp/file.php');
    }

    protected function tearDown(): void
    {
        FileSystem::delete(__DIR__ . '/temp');
    }
}
