<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Architecture;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ForbiddenTraitSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Traits are forbidden. Prefer service and constructor injection.';

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_TRAIT];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $file->addError(self::ERROR_MESSAGE, $position, self::class);
    }
}
