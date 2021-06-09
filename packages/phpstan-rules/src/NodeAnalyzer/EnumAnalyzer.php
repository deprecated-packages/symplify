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

    public function detect(Scope $scope, ClassLike $class): bool
    {
        if (! $class instanceof Class_) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection instanceof ClassReflection && $classReflection->isSubclassOf(Enum::class)) {
            return true;
        }

        return $this->hasEnumAnnotation($class);
    }

    private function hasEnumAnnotation(Class_ $class): bool
    {
        $docComment = $class->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        $simplePhpDoc = $this->simplePhpDocParser->parseDocBlock($docComment->getText());
        return (bool) $simplePhpDoc->getTagsByName('@enum');
    }
}
