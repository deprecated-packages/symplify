<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;

final class RemoverComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @var mixed[]
     */
    private $dataToRemove = [];

    /**
     * @param mixed[] $dataToRemove
     */
    public function __construct(array $dataToRemove)
    {
        $this->dataToRemove = $dataToRemove;
    }

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        foreach ($composerJson as $key => $values) {
            if (! isset($this->dataToRemove[$key])) {
                continue;
            }

            if (in_array($key, ['require', 'require-dev'], true)) {
                // process require*
                foreach ($this->dataToRemove[$key] as $package => $version) {
                    unset($composerJson[$key][$package]);
                }
            }
        }

        return $composerJson;
    }
}
