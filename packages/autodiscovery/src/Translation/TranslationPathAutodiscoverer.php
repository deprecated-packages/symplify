<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Translation;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\Finder\AutodiscoveryFinder;

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
     * @var AutodiscoveryFinder
     */
    private $autodiscoveryFinder;

    public function __construct(ContainerBuilder $containerBuilder, AutodiscoveryFinder $autodiscoveryFinder)
    {
        $this->containerBuilder = $containerBuilder;
        $this->autodiscoveryFinder = $autodiscoveryFinder;
    }

    public function autodiscover(): void
    {
        $paths = [];

        foreach ($this->autodiscoveryFinder->getTranslationDirectories() as $translationDirectory) {
            $paths[] = $translationDirectory->getRealPath();
        }

        $this->containerBuilder->prependExtensionConfig('framework', [
            'translator' => [
                'paths' => $paths,
            ],
        ]);
    }
}
