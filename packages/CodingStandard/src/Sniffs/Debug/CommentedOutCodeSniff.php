<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Debug;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Minus;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Parser;
use PhpParser\ParserFactory;

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

        // Process whole comment blocks at once, so skip all but the first token.
        if ($position > 0 && $tokens[$position]['code'] === $tokens[($position - 1)]['code']) {
            return;
        }

        $content = $this->turnCommentedCodeIntoPhpCode($file, $position, $tokens);
        $isCode = $this->isCodeContent($content);
        if ($isCode) {
            $file->addError(self::ERROR_MESSAGE, $position, self::class);
        }
    }

    /**
     * @param int $position
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

            if (count($tokens) === 1 && $this->guessIsTextCommentToken($tokens[0])) {
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

    private function guessIsTextCommentToken(Node $token): bool
    {
        if ($token instanceof ConstFetch) {
            return true;
        }

        if ($token instanceof Minus && ! $token->left instanceof Variable) {
            return true;
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
}
