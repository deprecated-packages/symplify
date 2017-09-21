<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\FixerTokenWrapper\PropertyWrapper;
use Symplify\CodingStandard\Tokenizer\ClassTokensAnalyzer;

final class BlockPropertyCommentFixer implements FixerInterface, DefinedFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Block comment should be used instead of one liner.',
            [
                new CodeSample('<?php
/** @var SomeType */
private $property;
'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_CLASS, T_TRAIT]) &&
            $tokens->isAllTokenKindsFound([T_VARIABLE, T_DOC_COMMENT]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(T_CLASS)) {
                continue;
            }

            $classTokenAnalyzer = ClassTokensAnalyzer::createFromTokensArrayStartPosition($tokens, $index);
            foreach ($classTokenAnalyzer->getProperties() as $propertyPosition => $propertyToken) {
                $propertyWrapper = PropertyWrapper::createFromTokensAndPosition($tokens, $propertyPosition);
                if (! $propertyWrapper->hasDocBlock()) {
                    continue;
                }

                $docBlockWrapper = $propertyWrapper->getDocBlockWrapper();
                $docBlockWrapper->setWhitespacesFixerConfig($this->whitespacesFixerConfig);

                if (! $docBlockWrapper->isSingleLine()) {
                    continue;
                }

                $docBlockWrapper->changeToMultiLine();
            }
        }
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getPriority(): int
    {
        return 0;
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
