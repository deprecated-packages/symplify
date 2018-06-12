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
        // no name of monorepo package => nothing to remove
        if (! isset($composerJson['name'])) {
            return $composerJson;
        }

        [$vendorName, ] = explode('/', $composerJson['name']);

        foreach ($composerJson as $key => $values) {
            if (! in_array($key, [Section::REQUIRE, Section::REQUIRE_DEV], true)) {
                continue;
            }

            foreach ($values as $package => $version) {
                if (! Strings::startsWith($package, $vendorName)) {
                    continue;
                }

                unset($composerJson[$key][$package]);
            }
        }

        return $composerJson;
    }
}
