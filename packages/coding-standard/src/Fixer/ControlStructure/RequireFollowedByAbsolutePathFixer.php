<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ControlStructure;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;

final class RequireFollowedByAbsolutePathFixer extends AbstractSymplifyFixer
{
    /**
     * @var int[]
     */
    private const INCLUDY_TOKEN_KINDS = [T_REQUIRE, T_REQUIRE_ONCE, T_INCLUDE, T_INCLUDE_ONCE];

    public function __construct()
    {
        trigger_error(sprintf(
            'Fixer "%s" is deprecated. Use instead "%s"',
            self::class,
            'https://github.com/rectorphp/rector/blob/master/docs/AllRectorsOverview.md#absolutizerequireandincludepathrector'
        ));

        sleep(3);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'include/require should be followed by absolute path.',
            [new CodeSample('require "vendor/autoload.php"')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::INCLUDY_TOKEN_KINDS)
            && $tokens->isTokenKindFound(T_CONSTANT_ENCAPSED_STRING);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(self::INCLUDY_TOKEN_KINDS)) {
                continue;
            }

            $nextTokenPosition = $tokens->getNextNonWhitespace($index);
            if ($nextTokenPosition === null) {
                continue;
            }

            $nextToken = $tokens[$nextTokenPosition];

            if ($this->shouldSkipToken($nextToken)) {
                continue;
            }

            $this->fixToken($tokens, $nextToken, $nextTokenPosition);
        }
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(ConcatSpaceFixer::class);
    }

    private function shouldSkipToken(Token $token): bool
    {
        if (! $token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
            return true;
        }

        return $this->startsWithPhar($token);
    }

    private function fixToken(Tokens $tokens, Token $token, int $tokenPosition): void
    {
        $tokensToAdd = [new Token([T_DIR, '__DIR__']), new Token('.')];

        if ($this->startsWithSlash($token)) {
            $tokensToAdd[] = $token;
        } else {
            $oldRequiredFile = $token->getContent();

            // remove "./" which would break the path
            if (Strings::startsWith($oldRequiredFile, '"')) {
                $quoteChar = '"';
                $oldRequiredFile = $this->processDoubleQuotePath($oldRequiredFile);
            } else {
                $quoteChar = "'";
                $oldRequiredFile = $this->processSingleQuotePath($oldRequiredFile);
            }

            if (! Strings::match($oldRequiredFile, '#(\'|")\/#')) {
                $oldRequiredFile = $quoteChar . '/' . $oldRequiredFile;
            }

            $tokensToAdd[] = new Token([T_CONSTANT_ENCAPSED_STRING, $oldRequiredFile]);
        }

        unset($tokens[$tokenPosition]);
        $tokens->insertAt($tokenPosition, $tokensToAdd);
    }

    private function startsWithPhar(Token $nextToken): bool
    {
        return Strings::startsWith($nextToken->getContent(), "'phar://");
    }

    private function startsWithSlash(Token $nextToken): bool
    {
        return Strings::startsWith($nextToken->getContent(), "'/");
    }

    private function processDoubleQuotePath(string $filePath): string
    {
        if (Strings::startsWith($filePath, '"./')) {
            return Strings::replace($filePath, '#^\"\.\/#', '"/');
        }

        return ltrim($filePath, '"');
    }

    private function processSingleQuotePath(string $filePath): string
    {
        if (Strings::startsWith($filePath, "'./")) {
            return Strings::replace($filePath, "#^\'\.\/#", "'/");
        }

        return ltrim($filePath, "'");
    }
}
