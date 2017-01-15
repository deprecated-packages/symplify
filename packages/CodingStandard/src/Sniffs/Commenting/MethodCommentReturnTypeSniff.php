<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Helper\Commenting\FunctionHelper;

/**
 * Rules:
 * - Getters should have @return tag or return type (except {@inheritdoc}).
 */
final class MethodCommentReturnTypeSniff implements Sniff
{
    /**
     * @var string
     */
    public const NAME = 'Symplify\CodingStandard.Commenting.MethodCommentReturnType';

    /**
     * @var string[]
     */
    private $getterMethodPrefixes = ['get', 'is', 'has', 'will', 'should'];

    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @var array
     */
    private $tokens;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_FUNCTION];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position) : void
    {
        $this->file = $file;
        $this->position = $position;
        $this->tokens = $file->getTokens();

        if ($this->shouldBeSkipped()) {
            return;
        }

        $file->addError(
            'Getters should have @return tag or return type (except {@inheritdoc}).',
            $position,
            null
        );
    }

    private function shouldBeSkipped() : bool
    {
        if ($this->guessIsGetterMethod() === false) {
            return true;
        }

        if ($this->hasMethodCommentReturnOrInheritDoc()) {
            return true;
        }

        $returnTypeHint = FunctionHelper::findReturnTypeHint($this->file, $this->position);
        if ($returnTypeHint) {
            return true;
        }

        return false;
    }

    private function guessIsGetterMethod() : bool
    {
        $methodName = $this->file->getDeclarationName($this->position);
        if ($this->isRawGetterName($methodName)) {
            return true;
        }
        if ($this->hasGetterNamePrefix($methodName)) {
            return true;
        }

        return false;
    }

    private function getMethodComment() : string
    {
        if (! $this->hasMethodComment()) {
            return '';
        }
        $commentStart = $this->file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $this->position - 1);
        $commentEnd = $this->file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $this->position - 1);
        return $this->file->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);
    }

    private function hasMethodCommentReturnOrInheritDoc() : bool
    {
        $comment = $this->getMethodComment();
        if (strpos($comment, '{@inheritdoc}') !== false) {
            return true;
        }
        if (strpos($comment, '@return') !== false) {
            return true;
        }
        return false;
    }

    private function hasMethodComment() : bool
    {
        $currentToken = $this->tokens[$this->position];
        $docBlockClosePosition = $this->file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $this->position);
        if ($docBlockClosePosition === false) {
            return false;
        }
        $docBlockCloseToken = $this->tokens[$docBlockClosePosition];
        return $docBlockCloseToken['line'] === ($currentToken['line'] - 1);
    }

    private function isRawGetterName(string $methodName) : bool
    {
        return in_array($methodName, $this->getterMethodPrefixes);
    }

    private function hasGetterNamePrefix(string $methodName) : bool
    {
        foreach ($this->getterMethodPrefixes as $getterMethodPrefix) {
            if (strpos($methodName, $getterMethodPrefix) === 0) {
                $endPosition = strlen($getterMethodPrefix);
                $firstLetterAfterGetterPrefix = $methodName[$endPosition];
                if (ctype_upper($firstLetterAfterGetterPrefix)) {
                    return true;
                }
            }
        }
        return false;
    }
}
