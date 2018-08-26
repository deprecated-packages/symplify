<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;

/**
 * Remove inter-dependencies in split packages from root,
 * e.g. symfony/console needs symfony/filesystem in package,
 * but it makes no sense to have symfony/filesystem in root of symfony/symfony.
 */
final class RootRemoveComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        $vendorName = $this->resolveVendorNameFromComposerJson($composerJson);
        if ($vendorName === null) {
            return $composerJson;
        }

        foreach ($composerJson as $key => $values) {
            if (! in_array($key, [Section::REQUIRE, Section::REQUIRE_DEV], true)) {
                continue;
            }

            $composerJson = $this->processRequires($composerJson, $values, $vendorName, $key);
        }

        return $composerJson;
    }

    /**
     * @param mixed[] $composerJson
     */
    private function resolveVendorNameFromComposerJson(array $composerJson): ?string
    {
        // no name of monorepo package => nothing to remove
        if (! isset($composerJson['name'])) {
            return null;
        }

        [$vendorName, ] = explode('/', $composerJson['name']);

        return $vendorName;
    }

    /**
     * @param mixed[] $composerJson
     * @param string[] $requires
     * @return mixed[]
     */
    private function processRequires(array $composerJson, array $requires, string $vendorName, string $key): array
    {
        foreach (array_keys($requires) as $package) {
            if (! Strings::startsWith($package, $vendorName . '/')) {
                continue;
            }

            unset($composerJson[$key][$package]);
        }

        return $composerJson;
    }
}
