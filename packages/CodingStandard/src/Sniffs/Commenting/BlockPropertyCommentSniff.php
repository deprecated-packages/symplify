<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Fixer\Commenting\BlockPropertyCommentFixer;
use Symplify\CodingStandard\SniffTokenWrapper\PropertyWrapper;

final class BlockPropertyCommentSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Block comment should be used instead of one liner.';

    public function __construct()
    {
        trigger_error(sprintf(
            'Class "%s" was deprecated in favor of "%s" that performs the same check better. Use it instead.',
            self::class,
            BlockPropertyCommentFixer::class
        ), E_USER_DEPRECATED);
    }

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
