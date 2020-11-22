<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\Collector;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use Symplify\StaticDetector\ValueObject\StaticClassMethod;
use Symplify\StaticDetector\ValueObject\StaticClassMethodWithStaticCalls;
use Symplify\StaticDetector\ValueObject\StaticReport;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class StaticNodeCollector
{
    /**
     * @var StaticClassMethod[]
     */
    private $staticClassMethods = [];

    /**
     * @var array<string, array<string, StaticCall[]>>
     */
    private $staticCalls = [];

    public function addStaticClassMethod(ClassMethod $classMethod, ClassLike $classLike): void
    {
        $class = (string) $classLike->namespacedName;
        $method = (string) $classMethod->name;

        $this->staticClassMethods[] = new StaticClassMethod($class, $method, $classMethod);
    }

    public function addStaticCall(StaticCall $staticCall, ?ClassLike $classLike = null): void
    {
        if ($staticCall->class instanceof Expr) {
            // weird expression, skip
            return;
        }

        if ($staticCall->name instanceof Expr) {
            // weird expression, skip
            return;
        }

        $class = $this->resolveClass($staticCall->class, $classLike);
        $method = (string) $staticCall->name;
        $this->staticCalls[$class][$method][] = $staticCall;
    }

    public function generateStaticReport(): StaticReport
    {
        return new StaticReport($this->getStaticClassMethodWithStaticCalls());
    }

    /**
     * @return StaticClassMethodWithStaticCalls[]
     */
    private function getStaticClassMethodWithStaticCalls(): array
    {
        $staticClassMethodWithStaticCalls = [];

        foreach ($this->staticClassMethods as $staticClassMethod) {
            $staticCalls = $this->staticCalls[$staticClassMethod->getClass()][$staticClassMethod->getMethod()] ?? [];

            $staticClassMethodWithStaticCalls[] = new StaticClassMethodWithStaticCalls(
                $staticClassMethod,
                $staticCalls
            );
        }

        return $staticClassMethodWithStaticCalls;
    }

    private function resolveClass(Name $staticClassName, ?ClassLike $classLike = null): string
    {
        $class = (string) $staticClassName;
        if (in_array($class, ['self', 'static'], true)) {
            if ($classLike === null) {
                throw new ShouldNotHappenException();
            }

            return (string) $classLike->namespacedName;
        }

        if ($class === 'parent') {
            if (! $classLike instanceof Class_) {
                throw new ShouldNotHappenException();
            }

            if ($classLike->extends === null) {
                throw new ShouldNotHappenException();
            }

            return (string) $classLike->extends;
        }

        return $class;
    }
}
