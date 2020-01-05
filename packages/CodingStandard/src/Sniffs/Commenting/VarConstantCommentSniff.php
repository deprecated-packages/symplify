<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;

final class VarConstantCommentSniff implements Sniff
{
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
        if (isset($propertyAnnotations['@var'])) {
            return;
        }

        $file->addError('Constant should have a docblock comment with "@var type".', $position, self::class);
    }
}
