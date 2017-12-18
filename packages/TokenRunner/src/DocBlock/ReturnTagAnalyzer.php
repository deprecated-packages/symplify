<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use Nette\Utils\Strings;

final class ReturnTagAnalyzer
{
    public function isReturnTagUseful(?string $docType, ?string $docDescription, ?string $returnType): bool
    {
        if ($returnType === null || $docType=== null) {
            return false;
        }

        if ($docDescription) {
            return true;
        }

        if ($returnType === $docType) {
            return false;
        }

        if ($returnType && Strings::endsWith($returnType, '\\' . $docType)) {
            return false;
        }

        // simple types
        if ($docType === 'boolean' && $returnType === 'bool') {
            return false;
        }

        if ($docType === 'integer' && $returnType === 'int') {
            return false;
        }

        return true;
    }
}
