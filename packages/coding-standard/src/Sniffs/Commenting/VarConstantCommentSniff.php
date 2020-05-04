<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;

/**
 * @deprecated
 */
final class VarConstantCommentSniff implements Sniff
{
    public function __construct()
    {
        trigger_error(sprintf(
            'Sniff "%s" is deprecated and will be removed in Symplify 8 (May 2020). Use "%s" instead',
            self::class,
            'https://github.com/rectorphp/rector/blob/master/docs/AllRectorsOverview.md#varconstantcommentrector'
        ));

        sleep(3);
    }

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
