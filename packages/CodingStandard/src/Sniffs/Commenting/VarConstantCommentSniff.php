<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;

final class VarConstantCommentSniff implements Sniff
{
    /**
     * @var string
     */
    private const VAR_ANNOTATION = '@var';

    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Constant should have docblock comment.';

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_CONST];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $propertyAnnotations = AnnotationHelper::getAnnotations($file, $position);

        if (! isset($propertyAnnotations[self::VAR_ANNOTATION])) {
            $file->addError(self::ERROR_MESSAGE, $position, self::class);
        }
    }
}
