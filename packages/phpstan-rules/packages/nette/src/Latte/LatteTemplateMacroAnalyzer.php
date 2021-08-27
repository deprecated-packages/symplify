<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Latte;

use Latte\Parser;
use Nette\Utils\FileSystem;

final class LatteTemplateMacroAnalyzer
{
    public function __construct(
        private Parser $latteParser
    ) {
    }

    public function hasMacro(string $templateFilePath, string $macroName): bool
    {
        $fileContents = FileSystem::read($templateFilePath);

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
