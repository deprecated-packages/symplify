<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Naming;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Naming\StandardizeHereNowDocKeywordFixer\StandardizeHereNowDocKeywordFixerTest
 */
final class StandardizeHereNowDocKeywordFixer extends AbstractSymplifyFixer implements ConfigurableFixerInterface, DocumentedRuleInterface
{
    /**
     * @api
     * @var string
     */
    public const KEYWORD = 'keyword';

    /**
     * @api
     * @var string
     */
    private const DEFAULT_KEYWORD = 'CODE_SAMPLE';

    /**
     * @see https://regex101.com/r/ED2b9V/1
     * @var string
     */
    private const START_HEREDOC_NOWDOC_NAME_REGEX = '#(<<<(\')?)(?<name>.*?)((\')?\s)#';

    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Use configured nowdoc and heredoc keyword';

    /**
     * @var string
     */
    private $keyword = self::DEFAULT_KEYWORD;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_START_HEREDOC, T_START_NOWDOC]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        // function arguments, function call parameters, lambda use()
        for ($position = count($tokens) - 1; $position >= 0; --$position) {
            /** @var Token $token */
            $token = $tokens[$position];

            if ($token->isGivenKind(T_START_HEREDOC)) {
                $this->fixStartToken($tokens, $token, $position);
            }

            if ($token->isGivenKind(T_END_HEREDOC)) {
                $this->fixEndToken($tokens, $token, $position);
            }
        }
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        $this->keyword = $configuration[self::KEYWORD] ?? self::DEFAULT_KEYWORD;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
$value = <<<'WHATEVER'
...
'WHATEVER'
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$value = <<<'CODE_SNIPPET'
...
'CODE_SNIPPET'
CODE_SAMPLE
,
                [
                    self::KEYWORD => 'CODE_SNIPPET',
                ]
            ),
        ]);
    }

    private function fixStartToken(Tokens $tokens, Token $token, int $position): void
    {
        $match = Strings::match($token->getContent(), self::START_HEREDOC_NOWDOC_NAME_REGEX);
        if (! isset($match['name'])) {
            return;
        }

        $newContent = Strings::replace(
            $token->getContent(),
            self::START_HEREDOC_NOWDOC_NAME_REGEX,
            '$1' . $this->keyword . '$4'
        );

        $tokens[$position] = new Token([$token->getId(), $newContent]);
    }

    private function fixEndToken(Tokens $tokens, Token $token, int $position): void
    {
        if ($token->getContent() === $this->keyword) {
            return;
        }

        $tokenContent = $token->getContent();
        $trimmedTokenContent = trim($tokenContent);

        $spaceEnd = '';
        if (PHP_VERSION_ID >= 70300 && $tokenContent !== $trimmedTokenContent) {
            $spaceEnd = substr($tokenContent, 0, strlen($tokenContent) - strlen($trimmedTokenContent));
        }

        $tokens[$position] = new Token([$token->getId(), $spaceEnd . $this->keyword]);
    }
}
