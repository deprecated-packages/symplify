<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Stmt\ClassLike;
use Symplify\Astral\Naming\SimpleNameResolver;

final class ClassAnalyzer
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveConstantNames(ClassLike $classLike): array
    {
        $constantNames = [];

        foreach ($classLike->getConstants() as $classConst) {
            /** @var string $constantName */
            $constantName = $this->simpleNameResolver->getName($classConst->consts[0]->name);
            $constantNames[] = $constantName;
        }

        return $constantNames;
    }
}
