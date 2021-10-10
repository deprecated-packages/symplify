<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler;

use Nette\Utils\Strings;
use PHPStan\Analyser\Error;

/**
 * @see \Symplify\TemplatePHPStanCompiler\Tests\ErrorSkipperTest
 */
final class ErrorSkipper
{
    /**
     * @param Error[] $errors
     * @param string[] $errorIgnores
     * @return Error[]
     */
    public function skipErrors(array $errors, array $errorIgnores): array
    {
        $filteredErrors = [];

        foreach ($errors as $error) {
            foreach ($errorIgnores as $errorIgnore) {
                if (Strings::match($error->getMessage(), $errorIgnore)) {
                    continue 2;
                }
            }

            $filteredErrors[] = $error;
        }

        return $filteredErrors;
    }
}
