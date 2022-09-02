<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;

final class AutowiredMethodPropertyAnalyzer
{
    /**
     * @var string
     * @see https://regex101.com/r/gn2P0C/1
     */
    private const REQUIRED_DOCBLOCK_REGEX = '#\*\s+@(required)\n?#';

    public function detect(ClassMethod $classMethod): bool
    {
        $hasRequiredAttribute = $this->hasAttributes($classMethod, ['Symfony\Contracts\Service\Attribute\Required']);

        $docComment = $classMethod->getDocComment();
        if (! $docComment instanceof Doc) {
            return $hasRequiredAttribute;
        }

        if ((bool) Strings::match($docComment->getText(), self::REQUIRED_DOCBLOCK_REGEX)) {
            return true;
        }

        return $hasRequiredAttribute;
    }

    /**
     * @param string[] $desiredAttributeClasses
     */
    private function hasAttributes(ClassMethod | Property $stmt, array $desiredAttributeClasses): bool
    {
        foreach ($stmt->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $attributeName = $attribute->name->toString();

                if (in_array($attributeName, $desiredAttributeClasses, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
