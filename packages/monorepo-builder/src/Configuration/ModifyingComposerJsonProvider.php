<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Configuration;

use Symplify\MonorepoBuilder\ComposerJsonObject\ComposerJsonFactory;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;

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

    public function __construct(ComposerJsonFactory $composerJsonFactory, array $dataToAppend, array $dataToRemove)
    {
        if ($dataToAppend) {
            $this->appendingComposerJson = $composerJsonFactory->createFromArray($dataToAppend);
        }

        if ($dataToRemove) {
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
