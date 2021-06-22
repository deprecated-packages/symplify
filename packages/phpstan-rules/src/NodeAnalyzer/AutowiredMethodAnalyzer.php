<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Symfony\Contracts\Service\Attribute\Required;
use Symplify\Astral\Naming\SimpleNameResolver;

final class AutowiredMethodAnalyzer
{
    /**
     * @var string
     * @see https://regex101.com/r/gn2P0C/1
     */
    private const REQUIRED_DOCBLOCK_REGEX = '#\*\s+@(required|inject)\n?#';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function detect(ClassMethod | Property $stmt): bool
    {
        $docComment = $stmt->getDocComment();
        if (! $docComment instanceof Doc) {
            return $this->hasAttributes($stmt, [Required::class, 'Nette\DI\Attributes\Inject']);
        }
        if (! (bool) Strings::match($docComment->getText(), self::REQUIRED_DOCBLOCK_REGEX)) {
            return $this->hasAttributes($stmt, [Required::class, 'Nette\DI\Attributes\Inject']);
        }
        return true;
    }

    /**
     * @param class-string[] $attributeClasses
     */
    private function hasAttributes(ClassMethod | Property $stmt, array $attributeClasses): bool
    {
        foreach ($stmt->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $attributeName = $this->simpleNameResolver->getName($attribute->name);
                if (in_array($attributeName, $attributeClasses, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
