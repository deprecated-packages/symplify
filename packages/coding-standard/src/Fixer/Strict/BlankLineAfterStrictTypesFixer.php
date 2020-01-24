<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Strict;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;

/**
 * Inspired at https://github.com/aidantwoods/PHP-CS-Fixer/tree/feature/DeclareStrictTypesFixer-split
 *
 * @thanks Aidan Woods
 */
final class BlankLineAfterStrictTypesFixer extends AbstractSymplifyFixer
{
    /**
     * @var Token[]|null
     */
    private static $cachedDeclareStrictTypeTokens;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function __construct(WhitespacesFixerConfig $whitespacesFixerConfig)
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Strict type declaration has to be followed by empty line',
            [new CodeSample('
<?php

declare(strict_types=1);
namespace SomeNamespace;')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_OPEN_TAG, T_WHITESPACE, T_DECLARE, T_STRING, '=', T_LNUMBER, ';']);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $sequenceLocation = $tokens->findSequence($this->getDeclareStrictTypeSequence(), 1, 15);
        if ($sequenceLocation === null) {
            return;
        }

        end($sequenceLocation);
        $semicolonPosition = (int) key($sequenceLocation);

        // empty file
        if (! isset($tokens[$semicolonPosition + 2])) {
            return;
        }

        $lineEnding = $this->whitespacesFixerConfig->getLineEnding();

        $tokens->ensureWhitespaceAtIndex($semicolonPosition + 1, 0, $lineEnding . $lineEnding);
    }

    /**
     * Generates: "declare(strict_types=1);"
     *
     * @return Token[]
     */
    public function getDeclareStrictTypeSequence(): array
    {
        if (self::$cachedDeclareStrictTypeTokens) {
            return self::$cachedDeclareStrictTypeTokens;
        }

        $tokens = [
            new Token([T_DECLARE, 'declare']),
            new Token('('),
            new Token([T_STRING, 'strict_types']),
            new Token('='),
            new Token([T_LNUMBER, '1']),
            new Token(')'),
            new Token(';'),
        ];

        return self::$cachedDeclareStrictTypeTokens = $tokens;
    }
}
