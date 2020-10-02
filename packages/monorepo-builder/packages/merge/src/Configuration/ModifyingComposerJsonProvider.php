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
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    public function __construct(ComposerJsonFactory $composerJsonFactory, ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
        $this->composerJsonFactory = $composerJsonFactory;
    }

    public function getRemovingComposerJson(): ?ComposerJson
    {
        $dataToRemove = $this->parameterProvider->provideArrayParameter(Option::DATA_TO_REMOVE);
        if ($dataToRemove === []) {
            return null;
        }

        return $this->composerJsonFactory->createFromArray($dataToRemove);
    }

    public function getAppendingComposerJson(): ?ComposerJson
    {
        $dataToAppend = $this->parameterProvider->provideArrayParameter(Option::DATA_TO_APPEND);
        if ($dataToAppend === []) {
            return null;
        }

        return $this->composerJsonFactory->createFromArray($dataToAppend);
    }
}
