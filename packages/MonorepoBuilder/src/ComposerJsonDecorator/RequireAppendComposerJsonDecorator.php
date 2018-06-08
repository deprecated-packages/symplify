<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;

final class RequireAppendComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        foreach ($composerJson as $key => $values) {
            if ($key !== 'require-dev') {
                continue;
            }

            $composerJson[$key] += [
                'phpstan/phpstan' => '^0.9',
                'tracy/tracy' => '^2.4',
                'slam/php-cs-fixer-extensions' => '^1.15',
            ];
        }

        return $composerJson;
    }
}
