<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Architecture;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * @deprecated Will be removed in 3.0.
 * Too strict rule - traits have their use cases.
 */
final class ForbiddenTraitSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Traits are forbidden. Prefer service and constructor injection.';

    public function __construct()
    {
        trigger_error(sprintf(
            'Class "%s" was deprecated. Too strict rule - traits have their use cases.',
            self::class
        ), E_USER_DEPRECATED);
    }

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_TRAIT];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $file->addError(self::ERROR_MESSAGE, $position, self::class);
    }
}
