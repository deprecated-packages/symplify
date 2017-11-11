<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Debug;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpParser\Error;
use PhpParser\Parser;
use PhpParser\ParserFactory;

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
     * @var Parser
     */
    private $parser;

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

        $isCode = $this->isCodeContent($content);
        if ($isCode) {
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
        $parser = $this->getParser();

        try {
            $tokens = $parser->parse($content);
            if ($tokens === null) {
                return false;
            }

            if (count($tokens) === 1 && (property_exists($tokens[0], 'stmts') && count($tokens[0]->stmts) < 2)) {
                return false;
            }
        } catch (Error $error) {
            return false;
        }

        return true;
    }

    private function getParser(): Parser
    {
        if ($this->parser) {
            return $this->parser;
        }

        return $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
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
