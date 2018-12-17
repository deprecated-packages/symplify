<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Twig;

use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Symplify\Autodiscovery\Tests\AbstractContainerAwareTestCase;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;
use Twig_Environment;
use function Safe\realpath;

/**
 * @covers \Symplify\Autodiscovery\Twig\TwigPathAutodiscoverer
 */
final class TwigPathAutodiscoveryTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TwigFilesystemLoader
     */
    private $twigFilesystemLoader;

    protected function setUp(): void
    {
        /** @var Twig_Environment $twigEnvironment */
        $twigEnvironment = $this->container->get('twig');

        $this->twigFilesystemLoader = $twigEnvironment->getLoader();
    }

    public function test(): void
    {
        $this->assertInstanceOf(FilesystemLoader::class, $this->twigFilesystemLoader);

        $this->assertCount(2, $this->twigFilesystemLoader->getPaths());

        $this->assertSame([
            realpath(__DIR__ . '/../KernelProjectDir/packages/ForTests/templates/'),
            realpath(__DIR__ . '/../KernelProjectDir/templates/'),
        ], $this->twigFilesystemLoader->getPaths());
    }
}
