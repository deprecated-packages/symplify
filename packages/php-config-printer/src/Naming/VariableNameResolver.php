<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Naming;

use Nette\Utils\Strings;

final class VariableNameResolver
{
    public function resolveFromType(string $classType): string
    {
        $shortClassName = Strings::after($classType, '\\', -1);
        if (! is_string($shortClassName)) {
            $shortClassName = $classType;
        }

        $normalizedShortClassName = $this->normalizeUpperCase($shortClassName);
        return lcfirst($normalizedShortClassName);
    }

    private function normalizeUpperCase(string $shortClassName): string
    {
        // turns $SOMEUppercase => $someUppercase
        for ($i = 0; $i <= strlen($shortClassName); ++$i) {
            if (ctype_upper($shortClassName[$i]) && $this->isNumberOrUpper($shortClassName[$i + 1])) {
                $shortClassName[$i] = strtolower($shortClassName[$i]);
            } else {
                break;
            }
        }

        return $shortClassName;
    }

    private function isNumberOrUpper(string $char): bool
    {
        if (ctype_upper($char)) {
            return true;
        }

        return ctype_digit($char);
    }
}
