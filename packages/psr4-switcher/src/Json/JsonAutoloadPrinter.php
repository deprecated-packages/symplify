<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\Json;

use Nette\Utils\Json;
use Symplify\Psr4Switcher\FileSystem\Psr4PathNormalizer;
use Symplify\Psr4Switcher\ValueObject\Psr4NamespaceToPaths;

final class JsonAutoloadPrinter
{
    /**
     * @var Psr4PathNormalizer
     */
    private $pathNormalizer;

    public function __construct(Psr4PathNormalizer $pathNormalizer)
    {
        $this->pathNormalizer = $pathNormalizer;
    }

    /**
     * @param Psr4NamespaceToPaths[] $psr4NamespaceToPaths
     */
    public function createJsonAutoloadContent(array $psr4NamespaceToPaths): string
    {
        $normalizedJsonArray = $this->pathNormalizer->normalizePsr4NamespaceToPathsToJsonsArray($psr4NamespaceToPaths);
        $composerData = [
            'autoload' => [
                'psr-4' => $normalizedJsonArray,
            ],
        ];

        return Json::encode($composerData, Json::PRETTY);
    }
}
