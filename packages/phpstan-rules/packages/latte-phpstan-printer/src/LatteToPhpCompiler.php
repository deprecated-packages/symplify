<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter;

use Latte\Parser;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\UnknownMacroAwareLatteCompiler;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @see \Symplify\PHPStanRules\LattePHPStanPrinter\Tests\LatteToPhpCompiler\LatteToPhpCompilerTest
 */
final class LatteToPhpCompiler
{
    public function __construct(
        private SmartFileSystem $smartFileSystem,
        private Parser $latteParser,
        private UnknownMacroAwareLatteCompiler $unknownMacroAwareLatteCompiler
    ) {
    }

    public function compileContent(string $templateFileContent): string
    {
        $latteTokens = $this->latteParser->parse($templateFileContent);

        return $this->unknownMacroAwareLatteCompiler->compile($latteTokens, 'DummyTemplateClass');
    }

    public function compileFilePath(string $templateFilePath): string
    {
        $templateFileContent = $this->smartFileSystem->readFile($templateFilePath);

        return $this->compileContent($templateFileContent);
    }
}
