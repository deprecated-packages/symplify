<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Reflection\ClassReflection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PHPStanRules\Printer\DuplicatedClassMethodPrinter;

/**
 * @implements Collector<InClassMethodNode, mixed[]|null>
 */
final class ClassMethodContentCollector implements Collector
{
    /**
     * @var array<class-string>
     */
    private const EXCLUDED_TYPES = [Kernel::class, Extension::class, TestCase::class];

    /**
     * @var string[]
     */
    private const EXCLUDED_METHOD_NAMES = ['getNodeType', 'getNodeTypes'];

    public function __construct(
        private DuplicatedClassMethodPrinter $duplicatedClassMethodPrinter,
    ) {
    }

    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    /**
     * @param InClassMethodNode $node
     * @return mixed[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        $classMethod = $node->getOriginalNode();
        if ($this->shouldSkipClassMethod($classMethod, $scope)) {
            return null;
        }

        $classMethodName = $classMethod->name->toString();
        if (in_array($classMethodName, self::EXCLUDED_METHOD_NAMES, true)) {
            return null;
        }

        $printedClassMethod = $this->duplicatedClassMethodPrinter->printClassMethod($classMethod);

        return [$classMethodName, $classMethod->getLine(), $printedClassMethod];
    }

    private function shouldSkipClassMethod(ClassMethod $classMethod, Scope $scope): bool
    {
        if ($classMethod->isMagic()) {
            return true;
        }

        // traits are to magic to analyse
        if ($scope->isInTrait()) {
            return true;
        }

        if (! $scope->isInClass()) {
            return true;
        }

        return $this->shouldSkipClassType($scope);
    }

    private function shouldSkipClassType(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        foreach (self::EXCLUDED_TYPES as $excludedType) {
            if ($classReflection->isSubclassOf($excludedType)) {
                return true;
            }
        }

        return false;
    }
}
