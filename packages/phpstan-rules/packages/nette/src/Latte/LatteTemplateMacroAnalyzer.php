<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Latte;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteTemplateMacroAnalyzer
{
    public function __construct(
        private SmartFileSystem $smartFileSystem
    ) {
    }

    /**
     * @param string[] $macroNames
     */
    public function hasMacros(string $templateFilePath, array $macroNames): bool
    {
        $fileContents = $this->smartFileSystem->readFile($templateFilePath);

        $macroRegex = '#{(' . implode('|', $macroNames) . ')\b#';

        $matches = Strings::match($fileContents, $macroRegex);
        return $matches !== null;
    }
}
