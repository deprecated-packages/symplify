<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Composer;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;

final class ComposerAutoloadResolver
{
    /**
     * @var string
     */
    private const COMPOSER_JSON_FILE = './composer.json';

    /**
     * @return array<string, string[]|string>
     */
    public function getPsr4Autoload(): array
    {
        if (! file_exists(self::COMPOSER_JSON_FILE)) {
            return [];
        }

        $fileContent = FileSystem::read(self::COMPOSER_JSON_FILE);
        $composerJsonContent = Json::decode($fileContent, Json::FORCE_ARRAY);

        $autoloadPsr4 = $composerJsonContent['autoload']['psr-4'] ?? [];
        $autoloadDevPsr4 = $composerJsonContent['autoload-dev']['psr-4'] ?? [];

        return array_merge($autoloadPsr4, $autoloadDevPsr4);
    }
}
