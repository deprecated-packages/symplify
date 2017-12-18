<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use Nette\Utils\Strings;

final class ParamAndReturnTagAnalyzer
{
    /**
     * @var string[]
     */
    private $usefulTypes = [];

    public function isTagUseful(?string $docType, ?string $docDescription, ?string $paramType): bool
    {
        if ($this->isMatch($docType, $paramType)) {
            return false;
        }

        if ($docDescription) {
            return true;
        }

        if ($this->isLongSimpleType($docType, $paramType)) {
            return false;
        }

        if ($docType === null) {
            return true;
        }

        if (Strings::contains($docType, '[]') || Strings::contains($docType, '|')) {
            return true;
        }

        if (in_array($docType, $this->usefulTypes, true)) {
            return true;
        }

        if ($paramType === null) {
            return in_array($docType, ['string', 'bool', 'resource', 'false', 'int', 'true'], true);
        }

        if ($docType && $paramType && ($docType !== $paramType)) {
            return true;
        }

        return false;
    }

    /**
     * @param string[] $usefulTypes
     */
    public function setUsefulTypes(array $usefulTypes): void
    {
        $this->usefulTypes = $usefulTypes;
    }

    private function isMatch(?string $docType, ?string $paramType): bool
    {
        if ($docType === $paramType) {
            return true;
        }

        if ($docType) {
            if (Strings::endsWith($docType, '\\' . $paramType)) {
                return true;
            }

            if (Strings::endsWith($paramType, '\\' . $docType)) {
                return true;
            }
        }

        return false;
    }

    private function isLongSimpleType(?string $docType, ?string $paramType): bool
    {
        if ($docType === 'boolean' && $paramType === 'bool') {
            return true;
        }

        if ($docType === 'integer' && $paramType === 'int') {
            return true;
        }

        return false;
    }
}
