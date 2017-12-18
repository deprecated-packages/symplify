<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use Nette\Utils\Strings;

final class ParamTagAnalyzer
{
    /**
     * @var string[]
     */
    private $usefulTypes = [];

    public function isParamTagUseful(?string $docType, ?string $docDescription, ?string $paramType): bool
    {
        if ($docType === $docDescription) {
            return false;
        }

        if ($docType === $paramType) {
            return false;
        }

        if ($docType && Strings::endsWith($docType, '\\' . $paramType)) {
            return false;
        }

        if ($docDescription) {
            return true;
        }

        // simple types
        if ($docType === 'boolean' && $paramType === 'bool') {
            return false;
        }

        if ($docType === 'integer' && $paramType === 'int') {
            return false;
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
