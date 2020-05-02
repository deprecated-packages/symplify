<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Architecture;

use BadFunctionCallException;
use BadMethodCallException;
use DomainException;
use InvalidArgumentException;
use LengthException;
use LogicException;
use OutOfBoundsException;
use OutOfRangeException;
use OverflowException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use RangeException;
use RuntimeException;
use Throwable;
use UnderflowException;
use UnexpectedValueException;

/**
 * @deprecated
 */
final class ExplicitExceptionSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_THROW];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        // check "throw new" construction
        $newPosition = $file->findNext(T_NEW, $position, $position + 3);
        if (! $newPosition) {
            return;
        }

        $exceptionNamePosition = $file->findNext([T_STRING], $position);
        $exceptionNameToken = $file->getTokens()[$exceptionNamePosition];

        $exceptionName = $exceptionNameToken['content'];

        if (! $this->isNativeExceptionName($exceptionName)) {
            return;
        }

        $file->addError(
            sprintf('Use explicit and informative exception names over generic ones like "%s".', $exceptionName),
            $position,
            self::class
        );
    }

    /**
     * Check against official list: http://php.net/manual/en/spl.exceptions.php
     */
    private function isNativeExceptionName(string $exceptionName): bool
    {
        return in_array($exceptionName, [
            Throwable::class,
            Throwable::class,
            BadFunctionCallException::class,
            BadMethodCallException::class,
            DomainException::class,
            InvalidArgumentException::class,
            LengthException::class,
            LogicException::class,
            OutOfBoundsException::class,
            OutOfRangeException::class,
            OverflowException::class,
            RangeException::class,
            RuntimeException::class,
            UnderflowException::class,
            UnexpectedValueException::class,
        ], true);
    }
}
