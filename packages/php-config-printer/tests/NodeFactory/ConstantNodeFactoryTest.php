<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Tests\NodeFactory;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPUnit\Framework\TestCase;
use Symplify\PhpConfigPrinter\NodeFactory\ConstantNodeFactory;

final class ConstantNodeFactoryTest extends TestCase
{
    private ConstantNodeFactory $constantNodeFactory;

    protected function setUp(): void
    {
        $this->constantNodeFactory = new ConstantNodeFactory();
    }

    public function testConstantFetchNode(): void
    {
        $constFetch = $this->constantNodeFactory->createConstant('PHP_VERSION');
        $this->assertInstanceOf(ConstFetch::class, $constFetch);
        /** @var ConstFetch $constFetch */
        $this->assertSame('PHP_VERSION', $constFetch->name->toString());
    }

    public function testClassConstantFetchNode(): void
    {
        $constFetch = $this->constantNodeFactory->createConstant('SomeClass::TEST');
        $this->assertInstanceOf(ClassConstFetch::class, $constFetch);
        /** @var ClassConstFetch $constFetch */
        /** @var Identifier $class */
        $class = $constFetch->class;
        /** @var Name $name */
        $name = $constFetch->name;
        $this->assertSame('SomeClass', $class->toString());
        $this->assertSame('TEST', $name->toString());
    }
}
