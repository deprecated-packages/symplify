<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;

final class ParamAndReturnTagAnalyzer
{
    /**
     * @var string[]
     */
    private $uselessTypes = [];

    /**
     * @param string[] $codeTypes
     */
    public function isTagUseful(?TypeNode $typeNode, ?string $docDescription, array $codeTypes): bool
    {
        if ($this->isMatch($typeNode, $codeTypes)) {
            return false;
        }

        if ($docDescription) {
            return true;
        }

        // not code type nor type in typehint is known
        if ($codeTypes === [] && $typeNode === null) {
            return false;
        }

        if ($typeNode instanceof IdentifierTypeNode) {
            if (in_array($typeNode->name, $this->uselessTypes, true)) {
                return false;
            }
        }

        if ($this->isLongSimpleType($typeNode, $codeTypes)) {
            return false;
        }

        return true;
    }

    /**
     * @param string[] $uselessTypes
     */
    public function setUselessTypes(array $uselessTypes): void
    {
        $this->uselessTypes = $uselessTypes;
    }

    /**
     * @param string[] $codeTypes
     */
    private function isMatch(?TypeNode $typeNode, array $codeTypes): bool
    {
        if ($typeNode === null && $codeTypes === []) {
            return true;
        }

        if ((string) $typeNode === implode('|', $codeTypes)) {
            return true;
        }

        if ($typeNode) {
            $typeNodeAsString = (string) $typeNode;
            $codeTypesAsString = implode('|', $codeTypes);

            if (Strings::endsWith($typeNodeAsString, '\\' . $codeTypesAsString)) {
                return true;
            }

            if (Strings::endsWith($codeTypesAsString, '\\' . $typeNodeAsString)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $codeTypes
     */
    private function isLongSimpleType(TypeNode $typeNode, array $codeTypes): bool
    {
        if (! $typeNode instanceof IdentifierTypeNode) {
            return false;
        }

        $codeType = array_pop($codeTypes);

        if ($typeNode->name === 'boolean' && $codeType === 'bool') {
            return true;
        }

        if ($typeNode->name === 'integer' && $codeType === 'int') {
            return true;
        }

        return false;
    }
}
