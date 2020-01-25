<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\Configuration\MergedPackagesCollector;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;
use Symplify\MonorepoBuilder\ValueObject\Section;

/**
 * Remove inter-dependencies in split packages from root,
 * e.g. symfony/console needs symfony/filesystem in package,
 * but it makes no sense to have symfony/filesystem in root of symfony/symfony.
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

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        foreach ($composerJson as $key => $values) {
            if (! in_array($key, [Section::REQUIRE, Section::REQUIRE_DEV], true)) {
                continue;
            }

            $composerJson = $this->processRequires($composerJson, $values, $key);
        }

        return $composerJson;
    }

    /**
     * @param mixed[] $composerJson
     * @param string[] $requires
     * @return mixed[]
     */
    private function processRequires(array $composerJson, array $requires, string $key): array
    {
        foreach (array_keys($requires) as $package) {
            if (! in_array($package, $this->mergedPackagesCollector->getPackages(), true)) {
                continue;
            }

            unset($composerJson[$key][$package]);
        }

        return $composerJson;
    }
}
