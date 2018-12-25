<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Translation;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\FileSystem;

/**
 * @see https://symfony.com/doc/current/translation.html#translation-resource-file-names-and-locations
 */
final class TranslationPathAutodiscoverer implements AutodiscovererInterface
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
        $this->fileSystem = new FileSystem($containerBuilder);
    }

    public function autodiscover(): void
    {
        $paths = [];

        foreach ($this->fileSystem->getTranslationDirectories() as $templateDirectory) {
            $paths[] = $templateDirectory->getRealPath();
        }

        $this->containerBuilder->prependExtensionConfig('framework', [
            'translator' => [
                'paths' => $paths,
            ],
        ]);
    }
}
