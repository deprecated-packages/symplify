<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use Nette\Utils\Strings;

final class ReturnTagAnalyzer
{
    /**
     * @var string[]
     */
    private $usefulTypes = [];

    public function isReturnTagUseful(?string $docType, ?string $docDescription, ?string $returnType): bool
    {
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

        if ($docType === null) {
            return true;
        }

        if (Strings::contains($docType, '[]')) {
            return true;
        }

        return in_array($docType, $this->usefulTypes, true);
    }

    /**
     * @param string[] $usefulTypes
     */
    public function setUsefulTypes(array $usefulTypes): void
    {
        $this->usefulTypes = $usefulTypes;
    }
}
