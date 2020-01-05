<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Twig;

use Symplify\Autodiscovery\Tests\Source\HttpKernel\AudiscoveryTestingKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * @covers \Symplify\Autodiscovery\Twig\TwigPathAutodiscoverer
 */
final class TwigPathAutodiscoveryTest extends AbstractKernelTestCase
{
    /**
     * @var FilesystemLoader
     */
    private $twigFilesystemLoader;

    protected function setUp(): void
    {
        $this->bootKernel(AudiscoveryTestingKernel::class);

        /** @var Environment $twigEnvironment */
        $twigEnvironment = static::$container->get('twig');

        $this->twigFilesystemLoader = $twigEnvironment->getLoader();
    }

    public function test(): void
    {
        $this->assertInstanceOf(FilesystemLoader::class, $this->twigFilesystemLoader);

        $this->assertCount(2, $this->twigFilesystemLoader->getPaths());

        $this->assertSame([
            realpath(__DIR__ . '/../Source/KernelProjectDir/packages/ForTests/templates/'),
            realpath(__DIR__ . '/../Source/KernelProjectDir/templates/'),
        ], $this->twigFilesystemLoader->getPaths());
    }
}
