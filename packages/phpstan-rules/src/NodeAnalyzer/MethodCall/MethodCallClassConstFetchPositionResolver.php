<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\MethodCall;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class MethodCallClassConstFetchPositionResolver
{
    /**
     * @return int[]
     */
    public function resolve(MethodCall $methodCall): array
    {
        $argPositions = [];

        foreach ($methodCall->getArgs() as $position => $arg) {
            if ($arg->value instanceof ClassConstFetch) {
                $classConstFetch = $arg->value;
                if (! $this->isEnumLikeClassConstFetch($classConstFetch)) {
                    continue;
                }

                if (! is_int($position)) {
                    throw new ShouldNotHappenException();
                }

                $argPositions[] = $position;
            }
        }

        return $argPositions;
    }

    private function isEnumLikeClassConstFetch(ClassConstFetch $classConstFetch): bool
    {
        if (! $classConstFetch->name instanceof Identifier) {
            return false;
        }

        $classConstName = $classConstFetch->name;
        if ($classConstFetch->class instanceof Expr) {
            return false;
        }

        if ($classConstFetch->class->toString() === 'self') {
            return false;
        }

        return $classConstName->toString() !== 'class';
    }
}
