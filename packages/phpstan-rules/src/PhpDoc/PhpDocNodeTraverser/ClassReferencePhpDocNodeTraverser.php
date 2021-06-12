<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc\PhpDocNodeTraverser;

use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\PhpDoc\ClassReferencePhpDocNodeVisitor;
use Symplify\SimplePhpDocParser\PhpDocNodeTraverser;
use Symplify\SimplePhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode;

final class ClassReferencePhpDocNodeTraverser
{
    /**
     * @var ClassReferencePhpDocNodeVisitor
     */
    private $classReferencePhpDocNodeVisitor;

    public function __construct(ClassReferencePhpDocNodeVisitor $classReferencePhpDocNodeVisitor)
    {
        $this->classReferencePhpDocNodeVisitor = $classReferencePhpDocNodeVisitor;
    }

    public function decoratePhpDocNode(
        SimplePhpDocNode $simplePhpDocNode,
        ClassReflection $classReflection
    ): SimplePhpDocNode {
        $phpDocNodeTraverser = new PhpDocNodeTraverser();

        $this->classReferencePhpDocNodeVisitor->configureClassName($classReflection->getName());
        $phpDocNodeTraverser->addPhpDocNodeVisitor($this->classReferencePhpDocNodeVisitor);

        $phpDocNodeTraverser->traverse($simplePhpDocNode);

        return $simplePhpDocNode;
    }
}
