<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\Routing;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\DecoratorInterface;
use Symplify\Statie\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class RouteDecorator implements DecoratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var RouteInterface[]
     */
    private $routes = [];

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function addRoute(RouteInterface $route): void
    {
        $this->routes[] = $route;
    }

    public function decorateFile(AbstractFile $file): void
    {
        foreach ($this->routes as $route) {
            if ($route->matches($file)) {
                $file->setOutputPath($route->buildOutputPath($file));
                $file->setRelativeUrl($route->buildRelativeUrl($file));

                return;
            }
        }

        $relativeDirectory = $this->getRelativeDirectory($file);
        $file->setOutputPath(
            $relativeDirectory . DIRECTORY_SEPARATOR . $file->getBaseName() . DIRECTORY_SEPARATOR . 'index.html'
        );
        $file->setRelativeUrl($relativeDirectory . DIRECTORY_SEPARATOR . $file->getBaseName());
    }

    private function getRelativeDirectory(AbstractFile $file): string
    {
        $sourceParts = explode(DIRECTORY_SEPARATOR, $this->configuration->getSourceDirectory());
        $sourceDirectory = array_pop($sourceParts);

        $relativeParts = explode($sourceDirectory, $file->getRelativeDirectory());
        return array_pop($relativeParts);
    }
}
