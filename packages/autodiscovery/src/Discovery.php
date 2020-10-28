<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\Autodiscovery\Doctrine\DoctrineEntityMappingAutodiscoverer;
use Symplify\Autodiscovery\Finder\AutodiscoveryFinder;
use Symplify\Autodiscovery\Routing\AnnotationRoutesAutodiscoverer;
use Symplify\Autodiscovery\Translation\TranslationPathAutodiscoverer;
use Symplify\Autodiscovery\Twig\TwigPathAutodiscoverer;

final class Discovery
{
    /**
     * @var AutodiscoveryFinder
     */
    private $autodiscoveryFinder;

    /**
     * @param string[] $packageDirectories
     */
    public function __construct(string $projectDirectory, array $packageDirectories = [])
    {
        $this->autodiscoveryFinder = new AutodiscoveryFinder($projectDirectory, $packageDirectories);
    }

    public function discoverTemplates(ContainerBuilder $containerBuilder): void
    {
        $twigPathAutodiscoverer = new TwigPathAutodiscoverer($containerBuilder, $this->autodiscoveryFinder);
        $twigPathAutodiscoverer->autodiscover();
    }

    public function discoverEntityMappings(ContainerBuilder $containerBuilder): void
    {
        $doctrineEntityMappingAutodiscoverer = new DoctrineEntityMappingAutodiscoverer(
            $containerBuilder,
            $this->autodiscoveryFinder
        );
        $doctrineEntityMappingAutodiscoverer->autodiscover();
    }

    public function discoverTranslations(ContainerBuilder $containerBuilder): void
    {
        $translationPathAutodiscoverer = new TranslationPathAutodiscoverer(
            $containerBuilder,
            $this->autodiscoveryFinder
        );
        $translationPathAutodiscoverer->autodiscover();
    }

    public function discoverRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $annotationRoutesAutodiscoverer = new AnnotationRoutesAutodiscoverer(
            $routeCollectionBuilder,
            $this->autodiscoveryFinder
        );
        $annotationRoutesAutodiscoverer->autodiscover();
    }
}
