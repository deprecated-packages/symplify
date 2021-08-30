<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use Latte\Compiler;
use Latte\Macros\CoreMacros;
use Latte\Parser;
use Latte\Token;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteVariableNamesResolver
{
    private Compiler $latteCompiler;

    public function __construct(
        private Parser $latteParser,
        private SmartFileSystem $smartFileSystem
    ) {
        $this->latteCompiler = new Compiler();
        CoreMacros::install($this->latteCompiler);
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $latteFileContent = $this->smartFileSystem->readFile($filePath);
        $latteTokens = $this->latteParser->parse($latteFileContent);

        $variableNames = [];
        foreach ($latteTokens as $latteToken) {
            if ($this->shouldSkipLatteToken($latteToken)) {
                continue;
            }

            $macroNode = $this->latteCompiler->openMacro($latteToken->name, $latteToken->value, $latteToken->modifiers);

            foreach ($macroNode->tokenizer->tokens as $macroToken) {
                // skip macro values that generate new local-only values
                if ($macroNode->name === 'foreach') {
                    if ($macroToken[0] === 'as') {
                        break;
                    }
                }

                if (! str_starts_with($macroToken[0], '$')) {
                    continue;
                }

                $variableName = ltrim($macroToken[0], '$');
                $variableNames[] = $variableName;
            }

            /**
             * mimics internal Compiler behavior - @see \Latte\Compiler::processMacroTag()
             */
            if ($latteToken->empty) {
                $this->latteCompiler->closeMacro($latteToken->name, '', '');
            }
        }

        return $variableNames;
    }

    private function shouldSkipLatteToken(Token $latteToken): bool
    {
        if ($latteToken->type !== 'macroTag') {
            return true;
        }

        // skip closing tags
        return $latteToken->closing;
    }
}
