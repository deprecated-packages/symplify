<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper\Naming;

use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

final class ImportsResolver
{
    /**
     * @return string[]
     */
    public static function getFromTokens(Tokens $tokens): array
    {
        $imports = [];

        $importUseIndexes = (new TokensAnalyzer($tokens))->getImportUseIndexes();
        foreach ($importUseIndexes as $importUseIndex) {
            $nameStartPosition = $tokens->getNextMeaningfulToken($importUseIndex);
            $nextToken = $tokens[$nameStartPosition];

            if ($nextToken->getContent() === 'function') {
                continue;
            }

            $name = ClassFqnResolver::resolveDataFromStart($tokens, $nameStartPosition);
            $imports[$name->getName()] = $name->getLastName();
        }

        return $imports;
    }
}
