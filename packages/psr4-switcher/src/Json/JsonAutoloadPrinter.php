<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\Json;

use Nette\Utils\Json;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\Psr4Switcher\FileSystem\Psr4PathNormalizer;
use Symplify\Psr4Switcher\ValueObject\Psr4NamespaceToPaths;

final class JsonAutoloadPrinter
{
    public function __construct(
        private Psr4PathNormalizer $psr4PathNormalizer
    ) {
    }

    /**
     * @param Psr4NamespaceToPaths[] $psr4NamespaceToPaths
     */
    public function createJsonAutoloadContent(array $psr4NamespaceToPaths): string
    {
        $normalizedJsonArray = $this->psr4PathNormalizer->normalizePsr4NamespaceToPathsToJsonsArray(
            $psr4NamespaceToPaths
        );
        $composerData = [
            ComposerJsonSection::AUTOLOAD => [
                'psr-4' => $normalizedJsonArray,
            ],
        ];

        return Json::encode($composerData, Json::PRETTY);
    }
}
