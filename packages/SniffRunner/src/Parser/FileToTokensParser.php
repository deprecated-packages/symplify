<?php declare(strict_types=1);

namespace Symplify\SniffRunner\Parser;

use Nette\Utils\FileSystem;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Tokenizers\PHP;
use stdClass;

final class FileToTokensParser
{
    /**
     * @var stdClass
     */
    private $legacyConfig;

    public function parseFromFilePath(string $filePath) : array
    {
        $fileContent = FileSystem::read($filePath);
        return (new PHP($fileContent, $this->getLegacyConfig()))->getTokens();

    }

    /**
     * @return Config|stdClass
     */
    private function getLegacyConfig()
    {
        if ($this->legacyConfig) {
            return $this->legacyConfig;
        }

        $config = new stdClass();
        $config->tabWidth = 4;
        $this->legacyConfig = $config;

        return $this->legacyConfig;
    }
}
