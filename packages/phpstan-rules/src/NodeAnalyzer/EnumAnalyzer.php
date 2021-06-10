<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use MyCLabs\Enum\Enum;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\PhpDoc\BarePhpDocParser;

final class EnumAnalyzer
{
    /**
     * @var BarePhpDocParser
     */
    private $barePhpDocParser;

    public function __construct(BarePhpDocParser $barePhpDocParser)
    {
        $this->barePhpDocParser = $barePhpDocParser;
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
        $phpPhpDocNode = $this->barePhpDocParser->parseNode($class);
        if (! $phpPhpDocNode instanceof PhpDocNode) {
            return false;
        }

        return (bool) $phpPhpDocNode->getTagsByName('@enum');
    }
}
