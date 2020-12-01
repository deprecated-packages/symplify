<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules;

use Symplify\SmartFileSystem\SmartFileSystem;

class ComposerAutoloadResolver
{
    /** @var SmartFileSystem */
    private $smartFileSystem;

    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }

    /**
     * @return array<string, string>
     */
    public function getPsr4Autoload(): array
    {
        $composerJsonFile = './composer.json';
        if (! file_exists($composerJsonFile)) {
            return [];
        }

        $composerJsonContent = json_decode($this->smartFileSystem->readFile($composerJsonFile), true);
        $autoloadPsr4 = $composerJsonContent['autoload']['psr-4'] ?? [];
        $autoloadDevPsr4 = $composerJsonContent['autoload-dev']['psr-4'] ?? [];

        return $autoloadPsr4 + $autoloadDevPsr4;
    }
}
