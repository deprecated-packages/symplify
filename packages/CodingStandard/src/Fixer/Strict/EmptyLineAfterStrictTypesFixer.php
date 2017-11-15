<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Strict;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\TokenBuilder;

/**
 * Inspired at https://github.com/aidantwoods/PHP-CS-Fixer/tree/feature/DeclareStrictTypesFixer-split
 *
 * @thanks Aidan Woods
 */
final class EmptyLineAfterStrictTypesFixer implements FixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Strict type declaration has to be followed by empty line.',
            [
                new CodeSample('
<?php declare(strict_types=1);
namespace SomeNamespace;')
            ]
        );
    }

    public function getPriority(): int
    {
        // must ran before SingleBlankLineBeforeNamespaceFixer, BlankLineAfterOpeningTagFixer and DeclareEqualNormalizeFixer.
        return 2;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_OPEN_TAG, T_DECLARE, T_STRING, T_LNUMBER]);
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $sequenceLocation = $tokens->findSequence(TokenBuilder::getDeclareStrictTypeSequence(), 1, 15);
        if ($sequenceLocation === null) {
            return;
        }

        end($sequenceLocation);
        $semicolonPosition = key($sequenceLocation);

        $lineEnding = $this->whitespacesFixerConfig->getLineEnding();

        $tokens->ensureWhitespaceAtIndex($semicolonPosition + 1, 0, $lineEnding . $lineEnding);
    }

    public function getName(): string
    {
        return self::class;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    public function setWhitespacesConfig(WhitespacesFixerConfig $whitespacesFixerConfig): void
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }
}
