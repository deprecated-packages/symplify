<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Parser;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\SmartFileSystem\SmartFileSystem;

final class FileToTokensParser
{
    public function __construct(
        private SmartFileSystem $smartFileSystem
    ) {
    }

    public function parseFromFilePath(string $filePath): Tokens
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return Tokens::fromCode($fileContent);
    }
}
