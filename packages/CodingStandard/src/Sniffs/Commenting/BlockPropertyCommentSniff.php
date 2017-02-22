<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\TokenWrapper\PropertyWrapper;

/**
 * Rules:
 * - Block comment should be used instead of one liner for properties.
 */
final class BlockPropertyCommentSniff implements Sniff
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var array
     */
    private $tokens;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_VARIABLE];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, int $position): void
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();

        $propertyWrapper = PropertyWrapper::createFromFileAndPosition($this->file, $position);
        if (! $docBlock = $propertyWrapper->getDocBlock()) {
            return;
        }

        if (! $docBlock->isSingleLine()) {
            return;
        }

        $fix = $file->addFixableError(
            'Block comment should be used instead of one liner',
            $position,
            self::class
        );

        if ($fix) {
            $docBlock->changeToMultiLine();
        }
    }
}
