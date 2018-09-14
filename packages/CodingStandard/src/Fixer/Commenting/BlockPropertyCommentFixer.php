<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;
use function Safe\sprintf;

/**
 * possible future-successor https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/3810
 */
final class BlockPropertyCommentFixer implements DefinedFixerInterface
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
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind([T_CLASS, T_TRAIT])) {
                continue;
            }

            $classWrapper = $this->classWrapperFactory->createFromTokensArrayStartPosition($tokens, $index);
            foreach ($classWrapper->getPropertyWrappers() as $propertyWrapper) {
                $docBlockWrapper = $propertyWrapper->getDocBlockWrapper();
                if ($docBlockWrapper === null || ! $docBlockWrapper->isSingleLine()) {
                    continue;
                }

                $tokens[$docBlockWrapper->getTokenPosition()] = new Token(
                    [T_DOC_COMMENT, $this->convertDocBlockToMultiline($docBlockWrapper->getContent())]
                );
            }
        }
    }

    /**
     * Must run before @see \PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer
     */
    public function getPriority(): int
    {
        return 1;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
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
