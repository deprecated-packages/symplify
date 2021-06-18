<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
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

    public function detect(ClassMethod $classMethod): bool
    {
        $docComment = $classMethod->getDocComment();
        if ($docComment instanceof Doc) {
            return (bool) Strings::match($docComment->getText(), self::REQUIRED_DOCBLOCK_REGEX);
        }

        foreach ($classMethod->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $attributeName = $this->simpleNameResolver->getName($attribute->name);
                return in_array($attributeName, [Required::class, 'Nette\DI\Attributes\Inject'], true);
            }
        }

        return false;
    }
}
