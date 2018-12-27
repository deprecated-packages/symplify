<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Twig;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\FileSystem;

/**
 * @see https://github.com/Haehnchen/idea-php-symfony2-plugin/issues/1216
 */
final class TwigPathAutodiscoverer implements AutodiscovererInterface
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(ContainerBuilder $containerBuilder, FileSystem $fileSystem)
    {
        $this->filesystem = $fileSystem;
        $this->containerBuilder = $containerBuilder;
    }

    public function autodiscover(): void
    {
        $paths = [];
        foreach ($this->filesystem->getTemplatesDirectories() as $templateDirectory) {
            $paths[] = $templateDirectory->getRealPath();
        }

        $this->containerBuilder->prependExtensionConfig('twig', [
            'paths' => $paths,
        ]);
    }
}
