<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;

final class AppenderComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @var mixed[]
     */
    private $dataToAppend = [];

    /**
     * @param mixed[] $dataToAppend
     */
    public function __construct(array $dataToAppend)
    {
        $this->dataToAppend = $dataToAppend;
    }

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        foreach ($composerJson as $key => $values) {
            if (! isset($this->dataToAppend[$key])) {
                continue;
            }

            $composerJson[$key] += $this->dataToAppend[$key];
        }

        return $composerJson;
    }
}
