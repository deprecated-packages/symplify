<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Helper\Whitespace;

use PHP_CodeSniffer\Files\File;

final class ClassMetrics
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $classPosition;

    /**
     * @var mixed[]
     */
    private $tokens = [];

    public function __construct(File $file, int $classPosition)
    {
        $this->file = $file;
        $this->classPosition = $classPosition;
        $this->tokens = $file->getTokens();
    }

    /**
     * @return false|int
     */
    public function getLineDistanceBetweenClassAndLastUseStatement()
    {
        $lastUseStatementPosition = $this->getLastUseStatementPosition();
        if (! $lastUseStatementPosition) {
            return false;
        }

        return (int) $this->tokens[$this->getClassPositionIncludingComment()]['line']
            - $this->tokens[$lastUseStatementPosition]['line']
            - 1;
    }

    /**
     * @return bool|int
     */
    public function getLastUseStatementPosition()
    {
        return $this->file->findPrevious(T_USE, $this->classPosition);
    }

    /**
     * @return bool|int
     */
    public function getLineDistanceBetweenNamespaceAndFirstUseStatement()
    {
        $namespacePosition = (int) $this->file->findPrevious(T_NAMESPACE, $this->classPosition);

        $nextUseStatementPosition = (int) $this->file->findNext(T_USE, $namespacePosition);
        if (! $nextUseStatementPosition) {
            return false;
        }

        if ($this->tokens[$nextUseStatementPosition]['line'] === 1 || $this->isInsideClass($nextUseStatementPosition)) {
            return false;
        }

        return $this->tokens[$nextUseStatementPosition]['line'] - $this->tokens[$namespacePosition]['line'] - 1;
    }

    /**
     * @return false|int
     */
    public function getLineDistanceBetweenClassAndNamespace()
    {
        $namespacePosition = $this->file->findPrevious(T_NAMESPACE, $this->classPosition);

        if (! $namespacePosition) {
            return false;
        }

        $classStartPosition = $this->getClassPositionIncludingComment();

        return $this->tokens[$classStartPosition]['line'] - $this->tokens[$namespacePosition]['line'] - 1;
    }

    /**
     * @return bool|int
     */
    private function getClassPositionIncludingComment()
    {
        $classStartPosition = $this->file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $this->classPosition);
        if ($classStartPosition) {
            return $classStartPosition;
        }

        return $this->classPosition;
    }

    private function isInsideClass(int $position): bool
    {
        $prevClassPosition = $this->file->findPrevious(T_CLASS, $position, null, false);
        if ($prevClassPosition) {
            return true;
        }

        return false;
    }
}
