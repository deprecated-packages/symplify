<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Nop;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethod\NoParentMethodCallOnEmptyStatementInParentMethodTest
 */
final class NoParentMethodCallOnEmptyStatementInParentMethod implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not call parent method if parent method is empty';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
    }

    public function getNodeType(): string
    {
        return StaticCall::class;
    }

    /**
     * @param StaticCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var Name $name */
        $name = $node->class;
        $classCaller = $name->parts[0];

        if ($classCaller !== 'parent') {
            return [];
        }

        $class = $node->getAttribute('parent');
        while ($class) {
            if ($class instanceof Class_) {
                break;
            }

            $class = $class->getAttribute('parent');
        }

        /** @var Identifier $method */
        $method = $node->name;
        $methodName = (string) $method->name;

        /** @var ClassReflection $classReflection */
        $classReflection = $scope->getClassReflection();
        /** @var ClassReflection $parentClass */
        $parentClass = $classReflection->getParentClass();
        $parentClassFileName = (string) $parentClass->getFileName();
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        /** @var Node $ast */
        $ast = $parser->parse((string) file_get_contents($parentClassFileName));

        /** @var ClassMethod[] $classMethods */
        $classMethods = $this->nodeFinder->findInstanceOf($ast, ClassMethod::class);
        foreach ($classMethods as $classMethod) {
            if ((string) $classMethod->name !== $methodName) {
                continue;
            }

            $stmts = $this->nodeFinder->findInstanceOf((array) $classMethod->getStmts(), Stmt::class);
            $countStmts = 0;
            foreach ($stmts as $stmt) {
                // ensure empty statement not counted
                if ($stmt instanceof Nop) {
                    continue;
                }
                ++$countStmts;
            }

            if ($countStmts === 0) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }
}
