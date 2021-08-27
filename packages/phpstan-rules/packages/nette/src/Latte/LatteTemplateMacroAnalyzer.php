<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Latte;

use Latte\Parser;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteTemplateMacroAnalyzer
{
    public function __construct(
        private Parser $latteParser,
        private SmartFileSystem $smartFileSystem
    ) {
    }

    public function hasMacro(string $templateFilePath, string $macroName): bool
    {
        $fileContents = $this->smartFileSystem->readFile($templateFilePath);

        $latteTokens = $this->latteParser->parse($fileContents);
        foreach ($latteTokens as $latteToken) {
            if ($latteToken->type !== 'macroTag') {
                continue;
            }

            if ($latteToken->name === $macroName) {
                return true;
            }
        }

        return false;
    }
}
