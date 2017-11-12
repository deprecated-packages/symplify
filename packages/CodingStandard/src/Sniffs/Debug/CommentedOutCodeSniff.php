<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Debug;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\SniffTokenWrapper\CommentCleaner;

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
     * @var CommentCleaner
     */
    private $commentCleaner;

    public function __construct()
    {
        $this->commentCleaner = new CommentCleaner();
    }

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

            $content .= $this->commentCleaner->clearFromComment($tokens[$i]['content']) . $file->eolChar;
        }

        $content = trim($content);
        $content .= ' ?>';

        return $content;
    }

    private function isCodeContent(string $content): bool
    {
        $tokens = token_get_all($content);

        foreach ($tokens as $index => $token) {
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

            // if first found is token => code
            if (in_array($token[1] ?? $token, self::$phpKeywords, true)) {
                if ($this->isException($tokens, $index, $token)) {
                    return false;
                }

                return true;
            }

            // if first found is variable => code
            if ($token[0] === T_VARIABLE) {
                return true;
            }
        }

        return false;
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

    /**
     * @param mixed[] $tokens
     * @param mixed[] $token
     */
    private function isException(array $tokens, int $index, array $token): bool
    {
        if ($token[1] === 'use') { // "use like"
            $nextMeaninfulToken = $tokens[$index + 2];
            if ($nextMeaninfulToken[0] === T_STRING) {
                if (ctype_upper($nextMeaninfulToken[1][0])) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }
}
