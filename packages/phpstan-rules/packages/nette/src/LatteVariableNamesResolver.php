<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use Latte\Compiler;
use Latte\Macros\CoreMacros;
use Latte\Parser;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteVariableNamesResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/suXCQF/1
     */
    private const VARIABLE_NAME_FOREACH_REGEX = '#\$(?<variable_name>[\w_]+)#';

    public function __construct(
        private Parser $latteParser,
        private SmartFileSystem $smartFileSystem
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $latteFileContent = $this->smartFileSystem->readFile($filePath);
        $latteTokens = $this->latteParser->parse($latteFileContent);

        $latteCompiler = new Compiler();
        CoreMacros::install($latteCompiler);

        $variableNames = [];
        foreach ($latteTokens as $latteToken) {
            if ($latteToken->type !== 'macroTag') {
                continue;
            }

            if ($latteToken->closing) {
                // skip closing tags
                continue;
            }

            $macroNode = $latteCompiler->expandMacro($latteToken->name, $latteToken->value);
            foreach ($macroNode->tokenizer->tokens as $macroToken) {
                if (! str_starts_with($macroToken[0], '$')) {
                    continue;
                }

                $variableName = ltrim($macroToken[0], '$');
                $variableNames[] = $variableName;
            }
        }

        return $variableNames;
    }
}
