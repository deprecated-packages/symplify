<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Configuration\MergedPackagesCollector;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;

/**
 * Remove inter-dependencies in split packages from root, e.g. symfony/console needs symfony/filesystem in package, but
 * it makes no sense to have symfony/filesystem in root of symfony/symfony.
 *
 * @see \Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator\RootRemoveComposerJsonDecorator\RootRemoveComposerJsonDecoratorTest
 */
final class RootRemoveComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @var MergedPackagesCollector
     */
    private $mergedPackagesCollector;

    public function __construct(MergedPackagesCollector $mergedPackagesCollector)
    {
        $this->mergedPackagesCollector = $mergedPackagesCollector;
    }

    public function decorate(ComposerJson $composerJson): void
    {
        $require = $this->filterOutMergedPackages($composerJson->getRequire());
        $composerJson->setRequire($require);

        $requireDev = $this->filterOutMergedPackages($composerJson->getRequireDev());
        $composerJson->setRequireDev($requireDev);
    }

    /**
     * @param mixed[] $require
     * @return mixed[]
     */
    private function filterOutMergedPackages(array $require): array
    {
        $requireKeys = array_keys($require);
        foreach ($requireKeys as $packageName) {
            if (! in_array($packageName, $this->mergedPackagesCollector->getPackages(), true)) {
                continue;
            }

            unset($require[$packageName]);
        }

        return $require;
    }
}
