<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff;

/**
 * @deprecated Will be removed in 3.0.
 * Use @see \SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff instead.
 */
final class VarPropertyCommentSniff implements Sniff
{
    /**
     * @var string
     */
    private const VAR_ANNOTATION = '@var';

    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Property should have docblock comment.';

    public function __construct()
    {
        trigger_error(sprintf(
            'Class "%s" was deprecated in favor of "%s" that performs the same check. Use it instead.',
            self::class,
            TypeHintDeclarationSniff::class
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
        if (! PropertyHelper::isProperty($file, $position)) {
            return;
        }

        $propertyAnnotations = AnnotationHelper::getAnnotations($file, $position);
        if (! isset($propertyAnnotations[self::VAR_ANNOTATION])) {
            $file->addError(self::ERROR_MESSAGE, $position, self::class);
        }
    }
}
