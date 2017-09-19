<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\SniffTokenWrapper\PropertyWrapper;

final class BlockPropertyCommentSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Block comment should be used instead of one liner.';

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_VARIABLE];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $propertyWrapper = PropertyWrapper::createFromFileAndPosition($file, $position);
        $docBlock = $propertyWrapper->getDocBlock();
        if (! $docBlock) {
            return;
        }

        if (! $docBlock->isSingleLine()) {
            return;
        }

        $fix = $file->addFixableError(self::ERROR_MESSAGE, $position, self::class);
        if ($fix) {
            $docBlock->changeToMultiLine();
        }
    }
}
