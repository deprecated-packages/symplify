<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Latte;

use Latte\Parser;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteToPhpCompiler
{
    public function __construct(
        private SmartFileSystem $smartFileSystem,
        private Parser $latteParser,
        private UnknownMacroAwareLatteCompiler $unknownMacroAwareLatteCompiler
    ) {
    }

    public function compileFilePath(string $filePath): string
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        $latteTokens = $this->latteParser->parse($fileContent);

        return $this->unknownMacroAwareLatteCompiler->compile($latteTokens, 'DummyTemplateClass');
    }
}
