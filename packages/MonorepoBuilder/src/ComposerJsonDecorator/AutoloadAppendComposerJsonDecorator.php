<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;

final class AutoloadAppendComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        foreach ($composerJson as $key => $values) {
            if ($key !== 'autoload-dev') {
                continue;
            }

            $composerJson[$key]['psr-4'] += [
                'Symplify\Tests\\' => 'tests',
            ];
        }

        return $composerJson;
    }
}
