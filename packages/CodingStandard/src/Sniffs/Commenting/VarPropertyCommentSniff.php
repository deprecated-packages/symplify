<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;

final class VarPropertyCommentSniff /*extends AbstractVariableSniff */ implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Property should have docblock comment.';

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
    public function process(File $file, $position): void
    {
        if ( ! PropertyHelper::isProperty($file, $position)) {
            return;
        }

        $propertyVarAnnotations = AnnotationHelper::getAnnotations($file, $position);
        if (! isset($propertyVarAnnotations['@var'])) {
            $file->addError(self::ERROR_MESSAGE, $position, self::class);
        }
    }
}
