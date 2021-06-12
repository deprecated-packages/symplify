<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Composer;

use Nette\Utils\Json;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ComposerAutoloadResolver
{
    /**
     * @var string
     */
    private const COMPOSER_JSON_FILE = './composer.json';

    public function __construct(
        private SmartFileSystem $smartFileSystem
    ) {
    }

    /**
     * @return array<string, string[]|string>
     */
    public function getPsr4Autoload(): array
    {
        if (! file_exists(self::COMPOSER_JSON_FILE)) {
            return [];
        }

        $fileContent = $this->smartFileSystem->readFile(self::COMPOSER_JSON_FILE);
        $composerJsonContent = Json::decode($fileContent, Json::FORCE_ARRAY);

        $autoloadPsr4 = $composerJsonContent[ComposerJsonSection::AUTOLOAD]['psr-4'] ?? [];
        $autoloadDevPsr4 = $composerJsonContent[ComposerJsonSection::AUTOLOAD_DEV]['psr-4'] ?? [];

        return $autoloadPsr4 + $autoloadDevPsr4;
    }
}
