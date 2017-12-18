<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use Nette\Utils\Strings;

final class ParamTagAnalyzer
{
    public function isParamTagUseful(?string $docType, ?string $docDescription, ?string $paramType): bool
    {
        if ($docType === $docDescription) {
            return false;
        }

        if ($docType === 'mixed') {
            return false;
        }

        if ($docType === $paramType) {
            return false;
        }

        if ($docType && Strings::endsWith($docType, '\\' . $paramType)) {
            return false;
        }

        // simple types
        if ($docType === 'boolean' && $paramType === 'bool') {
            return false;
        }

        return true;
    }
}
