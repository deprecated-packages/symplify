<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Debug;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Checks 2+ lines with comments in a row.
 */
final class CommentedOutCodeSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'This comment is valid code. Uncomment it or remove it.';

    /**
     * @var string[]
     */
    private static $phpKeywords = [
        '__halt_compiler()', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case',
        'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif',
        'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends',
        'final', 'finally', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include',
        'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print',
        'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait',
        'try', 'unset', 'use', 'var', 'while', 'xor', 'yield',
    ];

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_COMMENT];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $tokens = $file->getTokens();

        if ($this->shouldSkip($file, $position, $tokens)) {
            return;
        }

        $content = $this->turnCommentedCodeIntoPhpCode($file, $position, $tokens);

        if ($this->isCodeContent($content)) {
            $file->addError(self::ERROR_MESSAGE, $position, self::class);
        }
    }

    /**
     * @param mixed[] $tokens
     */
    private function turnCommentedCodeIntoPhpCode(File $file, int $position, array $tokens): string
    {
        $content = '<?php ';

        for ($i = $position; $i < $file->numTokens; ++$i) {
            if ($tokens[$position]['code'] !== $tokens[$i]['code']) {
                break;
            }

            $content .= $this->trimCodeComments($tokens, $i) . $file->eolChar;
        }

        $content = trim($content);
        $content .= ' ?>';

        return $content;
    }

    /**
     * @param string[] $tokens
     */
    private function trimCodeComments(array $tokens, int $i): string
    {
        $tokenContent = trim($tokens[$i]['content']);
        $tokenContent = $this->trimCommentStart($tokenContent);
        $tokenContent = $this->trimContentBody($tokenContent);
        $tokenContent = $this->trimCommentEnd($tokenContent);

        return $tokenContent;
    }

    private function isCodeContent(string $content): bool
    {
        $tokens = token_get_all($content);

        foreach ($tokens as $token) {
            // if first found is string => comment
            if ($token[0] === T_STRING) {
                if (in_array($token[1], self::$phpKeywords, true)) {
                    continue;
                }

                return false;
            }

            if ($token[0] === T_WHITESPACE) {
                continue;
            }

            // if first found is variable => code
            if (in_array($token[1] ?? $token, self::$phpKeywords, true)) {
                return true;
            }
        }

        return false;
    }

    private function trimCommentStart(string $tokenContent): string
    {
        if (substr($tokenContent, 0, 2) === '//') {
            $tokenContent = substr($tokenContent, 2);
        }

        if (substr($tokenContent, 0, 1) === '#') {
            $tokenContent = substr($tokenContent, 1);
        }

        if (substr($tokenContent, 0, 3) === '/**') {
            $tokenContent = substr($tokenContent, 3);
        }

        if (substr($tokenContent, 0, 2) === '/*') {
            $tokenContent = substr($tokenContent, 2);
        }

        return $tokenContent;
    }

    private function trimCommentEnd(string $tokenContent): string
    {
        if (substr($tokenContent, -2) === '*/') {
            $tokenContent = substr($tokenContent, 0, -2);
        }

        return $tokenContent;
    }

    private function trimContentBody(string $tokenContent): string
    {
        if (isset($tokenContent[0]) && $tokenContent[0] === '*') {
            $tokenContent = substr($tokenContent, 1);
        }

        return $tokenContent;
    }

    /**
     * @param mixed[] $tokens
     */
    private function shouldSkip(File $file, int $position, array $tokens): bool
    {
        // is only single line of comment in the file
        $possibleNextCommentToken = $file->findNext(T_COMMENT, $position + 1);
        if ($possibleNextCommentToken === false) {
            return true;
        }

        // is one standalone line, skip it
        return ($tokens[$possibleNextCommentToken]['line'] - $tokens[$position]['line']) > 1;
    }
}
