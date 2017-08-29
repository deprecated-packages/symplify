<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Property;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\TokenWrapper\ClassWrapper;

final class DynamicPropertySniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Properties should be used instead of dynamically defined properties.';

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @var File
     */
    private $file;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_OBJECT_OPERATOR];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();

        if (! $this->isLocalPropertyAccess($position)) {
            return;
        }

        $propertyName = $this->tokens[$position + 1]['content'];

        $classWrapper = $this->getClassWrapper();
        if (in_array($propertyName, $classWrapper->getPropertyNames())) {
            return;
        }

        $file->addError(self::ERROR_MESSAGE, $position, self::class);
    }

    private function isLocalPropertyAccess($position): bool
    {
        $previousToken = $this->tokens[$position - 1];
        $nextToken = $this->tokens[$position + 1];

        return $previousToken['content'] === '$this' && $nextToken['code'] === T_STRING;
    }

    private function getClassWrapper(): ClassWrapper
    {
        $classTokenPosition = $this->file->findNext(T_CLASS, 1);

        return ClassWrapper::createFromFileAndPosition($this->file, $classTokenPosition);
    }
}
