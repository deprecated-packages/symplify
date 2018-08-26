<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;
use Symplify\PackageBuilder\Yaml\ParametersMerger;

final class AppenderComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @var mixed[]
     */
    private $dataToAppend = [];

    /**
     * @var ParametersMerger
     */
    private $parametersMerger;

    /**
     * @param mixed[] $dataToAppend
     */
    public function __construct(array $dataToAppend, ParametersMerger $parametersMerger)
    {
        $this->dataToAppend = $dataToAppend;
        $this->parametersMerger = $parametersMerger;
    }

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        foreach (array_keys($composerJson) as $key) {
            if (! isset($this->dataToAppend[$key])) {
                continue;
            }

            $composerJson[$key] = $this->parametersMerger->merge($this->dataToAppend[$key], $composerJson[$key]);
        }

        return $composerJson;
    }
}
