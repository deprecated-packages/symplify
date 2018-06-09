<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

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
            if (! in_array($key, ['autoload', 'autoload-dev'], true)) {
                continue;
            }

            foreach ($values as $autoloadType => $autoloadPaths) {
                if ($autoloadType !== 'psr-4') {
                    continue;
                }

                ksort($autoloadPaths);
                $composerJson[$key][$autoloadType] = $autoloadPaths;
            }
        }

        return $composerJson;
    }
}
