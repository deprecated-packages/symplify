<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;

final class SortAutoloadComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        foreach ($composerJson as $key => $values) {
            if (! in_array($key, [Section::AUTOLOAD, Section::AUTOLOAD_DEV], true)) {
                continue;
            }

            foreach ($values as $autoloadType => $autoloadPaths) {
                ksort($autoloadPaths);
                $composerJson[$key][$autoloadType] = $autoloadPaths;
            }
        }

        return $composerJson;
    }
}
