<?php

declare(strict_types=1);

namespace Symplify\Astral\Tests\NodeValue;

use Iterator;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\MagicConst\File;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PHPUnit\Framework\TestCase;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\Astral\StaticFactory\SimpleNameResolverStaticFactory;
use Symplify\PackageBuilder\Php\TypeChecker;

final class NodeValueResolverTest extends TestCase
{
    private NodeValueResolver $nodeValueResolver;

    protected function setUp(): void
    {
        $simpleNameResolver = SimpleNameResolverStaticFactory::create();
        $simpleNodeFinder = new SimpleNodeFinder(new TypeChecker(), new NodeFinder());
        $this->nodeValueResolver = new NodeValueResolver($simpleNameResolver, new TypeChecker(), $simpleNodeFinder);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(Expr $expr, string $expectedValue): void
    {
        $resolvedValue = $this->nodeValueResolver->resolve($expr, __FILE__);
        $this->assertSame($expectedValue, $resolvedValue);
    }

    /**
     * @return Iterator<string[]|Node[]>
     */
    public function provideData(): Iterator
    {
        yield [new String_('value'), 'value'];
        yield [new Concat(new Dir(), new String_('/example.latte')), __DIR__ . '/example.latte'];
        $args = [new Arg(new String_('.php')), new Arg(new String_('.latte')), new Arg(new File())];
        yield [new FuncCall(new Name('str_replace'), $args), __DIR__ . '/NodeValueResolverTest.latte'];
    }
}
