<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\CleanCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use function Safe\sprintf;

final class ForbiddenReferenceSniff implements Sniff
{
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
        $previousToken = $file->getTokens()[$position - 1];
        if ($previousToken['code'] !== T_BITWISE_AND) {
            return;
        }

        $file->addError(
            sprintf('Use explicit return values over magic "&%s" reference', $file->getTokens()[$position]['content']),
            $position,
            self::class
        );
    }
}
