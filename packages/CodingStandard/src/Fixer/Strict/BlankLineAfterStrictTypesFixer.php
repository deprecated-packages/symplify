<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Strict;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\TokenRunner\Builder\FixerBuilder\TokenBuilder;

/**
 * Inspired at https://github.com/aidantwoods/PHP-CS-Fixer/tree/feature/DeclareStrictTypesFixer-split
 *
 * @thanks Aidan Woods
 */
final class BlankLineAfterStrictTypesFixer implements DefinedFixerInterface
{
    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var TokenBuilder
     */
    private $tokenBuilder;

    public function __construct(TokenBuilder $tokenBuilder, WhitespacesFixerConfig $whitespacesFixerConfig)
    {
        $this->tokenBuilder = $tokenBuilder;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Strict type declaration has to be followed by empty line',
            [new CodeSample('
<?php declare(strict_types=1);
namespace SomeNamespace;')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_OPEN_TAG, T_WHITESPACE, T_DECLARE, T_STRING, '=', T_LNUMBER, ';']);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $sequenceLocation = $tokens->findSequence($this->tokenBuilder->getDeclareStrictTypeSequence(), 1, 15);
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
     * Must run after @see \PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer
     */
    public function getPriority(): int
    {
        return 0;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    public function isRisky(): bool
    {
        return false;
    }
}
