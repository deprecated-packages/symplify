<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;

/**
 * possible future-successor https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/3810
 */
final class BlockPropertyCommentFixer extends AbstractSymplifyFixer
{
    /**
     * @var ClassWrapperFactory
     */
    private $classWrapperFactory;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function __construct(
        ClassWrapperFactory $classWrapperFactory,
        WhitespacesFixerConfig $whitespacesFixerConfig
    ) {
        $this->classWrapperFactory = $classWrapperFactory;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Block comment should be used instead of one liner.',
            [new CodeSample('<?php
/** @var SomeType */
private $property;
')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_CLASS, T_TRAIT]) &&
            $tokens->isAllTokenKindsFound([T_VARIABLE, T_DOC_COMMENT]) &&
            $tokens->isAnyTokenKindsFound([T_PUBLIC, T_PROTECTED, T_PRIVATE]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->getReversedClassAndTraitPositions($tokens) as $index) {
            $classWrapper = $this->classWrapperFactory->createFromTokensArrayStartPosition($tokens, $index);
            foreach ($classWrapper->getPropertyWrappers() as $propertyWrapper) {
                $docBlockWrapper = $propertyWrapper->getDocBlockWrapper();
                if ($docBlockWrapper === null || ! $docBlockWrapper->isSingleLine()) {
                    continue;
                }

                $multilineContent = $this->convertDocBlockToMultiline($docBlockWrapper->getContent());
                $tokens[$docBlockWrapper->getTokenPosition()] = new Token([T_DOC_COMMENT, $multilineContent]);
            }
        }
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(PhpdocVarWithoutNameFixer::class);
    }

    private function convertDocBlockToMultiline(string $docBlock): string
    {
        $match = Strings::match($docBlock, '#\/\*\*(\s+)(?<content>.*?)(\s+)\*\/#');
        // missed match
        if (! isset($match['content'])) {
            return $docBlock;
        }

        $newLineIndent = $this->whitespacesFixerConfig->getLineEnding() . $this->whitespacesFixerConfig->getIndent();

        // in case of more annotations ina a row: "@var ... @inject"
        $docBlockContent = str_replace([' @'], [$newLineIndent . ' * @'], $match['content']);

        return sprintf('/**%s * %s%s */', $newLineIndent, $docBlockContent, $newLineIndent);
    }
}
