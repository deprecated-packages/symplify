<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use MyCLabs\Enum\Enum;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\SimplePhpDocParser\SimplePhpDocParser;

final class EnumAnalyzer
{
    /**
     * @var SimplePhpDocParser
     */
    private $simplePhpDocParser;

    public function __construct(SimplePhpDocParser $simplePhpDocParser)
    {
        $this->simplePhpDocParser = $simplePhpDocParser;
    }

    public function detect(Scope $scope, ClassLike $classLike): bool
    {
        if (! $classLike instanceof Class_) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return $this->hasEnumAnnotation($classLike);
        }
        if (! $classReflection->isSubclassOf(Enum::class)) {
            return $this->hasEnumAnnotation($classLike);
        }
        return true;
    }

    private function hasEnumAnnotation(Class_ $class): bool
    {
        $docComment = $class->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        $simplePhpDocNode = $this->simplePhpDocParser->parseDocBlock($docComment->getText());
        return (bool) $simplePhpDocNode->getTagsByName('@enum');
    }
}
