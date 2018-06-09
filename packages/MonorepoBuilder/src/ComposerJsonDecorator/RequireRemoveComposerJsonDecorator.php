<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;

final class RequireRemoveComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        foreach ($composerJson as $key => $values) {
            if ($key !== 'require') {
                continue;
            }

            foreach ($values as $package => $version) {
                if (! in_array($package, ['phpunit/phpunit', 'tracy/tracy'], true)) {
                    continue;
                }

                unset($composerJson['require'][$package]);
            }
        }

        return $composerJson;
    }
}
