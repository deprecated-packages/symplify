<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt;
use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\Reflection\ClassReflection;

final class ApiDocStmtAnalyzer
{
    public function isApiDoc(Stmt $stmt, ClassReflection $classReflection): bool
    {
        if ($classReflection->getResolvedPhpDoc() instanceof ResolvedPhpDocBlock) {
            $resolvedPhpDoc = $classReflection->getResolvedPhpDoc();
            if (str_contains($resolvedPhpDoc->getPhpDocString(), '@api')) {
                return true;
            }
        }

        $docComment = $stmt->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        return str_contains($docComment->getText(), '@api');
    }
}
