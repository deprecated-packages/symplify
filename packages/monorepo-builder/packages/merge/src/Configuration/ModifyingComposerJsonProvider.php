<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Configuration;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class ModifyingComposerJsonProvider
{
    /**
     * @var ComposerJson|null
     */
    private $appendingComposerJson;

    /**
     * @var ComposerJson|null
     */
    private $removingComposerJson;

    public function __construct(ComposerJsonFactory $composerJsonFactory, ParameterProvider $parameterProvider)
    {
        $dataToAppend = $parameterProvider->provideArrayParameter(Option::DATA_TO_APPEND);
        if ($dataToAppend !== []) {
            $this->appendingComposerJson = $composerJsonFactory->createFromArray($dataToAppend);
        }

        $dataToRemove = $parameterProvider->provideArrayParameter(Option::DATA_TO_REMOVE);
        if ($dataToRemove !== []) {
            $this->removingComposerJson = $composerJsonFactory->createFromArray($dataToRemove);
        }
    }

    public function getRemovingComposerJson(): ?ComposerJson
    {
        return $this->removingComposerJson;
    }

    public function getAppendingComposerJson(): ?ComposerJson
    {
        return $this->appendingComposerJson;
    }
}
