<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use Latte\Parser;
use Nette\Utils\Strings;
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

        $variableNames = [];
        foreach ($latteTokens as $latteToken) {
            if ($latteToken->type !== 'macroTag') {
                continue;
            }

            if ($latteToken->name === 'foreach') {
                $match = Strings::match($latteToken->value, self::VARIABLE_NAME_FOREACH_REGEX);
                if (! isset($match['variable_name'])) {
                    continue;
                }

                $variableNames[] = (string) $match['variable_name'];
            } else {
                $variableName = ltrim($latteToken->value, '$');
                $variableNames[] = $variableName;
            }
        }

        return $variableNames;
    }
}
