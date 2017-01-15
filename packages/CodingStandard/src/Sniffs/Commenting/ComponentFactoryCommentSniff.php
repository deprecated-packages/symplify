<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Helper\Commenting\FunctionHelper;

/**
 * Rules:
 * - CreateComponent* method should have a doc comment.
 * - CreateComponent* method should have a return tag.
 * - Return tag should contain type.
 */
final class ComponentFactoryCommentSniff implements Sniff
{
    /**
     * @var string
     */
    public const NAME = 'Symplify\CodingStandard.Commenting.ComponentFactoryComment';

    /**
     * @var int
     */
    private $position;

    /**
     * @var File
     */
    private $file;

    /**
     * @var array
     */
    private $tokens;

    public function register() : array
    {
        return [T_FUNCTION];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position)
    {
        $this->file = $file;
        $this->position = $position;
        $this->tokens = $file->getTokens();

        if (! $this->isComponentFactoryMethod()) {
            return;
        }

        $returnTypeHint = FunctionHelper::findReturnTypeHint($file, $position);
        if ($returnTypeHint) {
            return;
        }

        $commentEnd = $this->getCommentEnd();
        if (! $this->hasMethodComment($commentEnd)) {
            $file->addError(
                'createComponent<name> method should have a doc comment or return type.',
                $position,
                null
            );
            return;
        }

        $commentStart = $this->tokens[$commentEnd]['comment_opener'];
        $this->processReturnTag($commentStart);
    }

    private function isComponentFactoryMethod() : bool
    {
        $functionName = $this->file->getDeclarationName($this->position);
        return (strpos($functionName, 'createComponent') === 0);
    }

    /**
     * @return bool|int
     */
    private function getCommentEnd()
    {
        return $this->file->findPrevious(T_WHITESPACE, ($this->position - 3), null, true);
    }

    private function hasMethodComment(int $position) : bool
    {
        if ($this->tokens[$position]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            return true;
        }
        return false;
    }

    private function processReturnTag(int $commentStartPosition) : void
    {
        $return = null;
        foreach ($this->tokens[$commentStartPosition]['comment_tags'] as $tag) {
            if ($this->tokens[$tag]['content'] === '@return') {
                $return = $tag;
            }
        }
        if ($return !== null) {
            $content = $this->tokens[($return + 2)]['content'];
            if (empty($content) === true || $this->tokens[($return + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                $error = 'Return tag should contain type';
                $this->file->addError($error, $return, null);
            }
        } else {
            $this->file->addError(
                'CreateComponent* method should have a @return tag',
                $this->tokens[$commentStartPosition]['comment_closer'],
                null
            );
        }
    }
}
