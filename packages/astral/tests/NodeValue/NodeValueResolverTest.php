<?php

declare(strict_types=1);

namespace Symplify\Astral\Tests\NodeValue;

use Iterator;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PHPUnit\Framework\TestCase;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\Astral\StaticFactory\SimpleNameResolverStaticFactory;
use Symplify\PackageBuilder\Php\TypeChecker;

final class NodeValueResolverTest extends TestCase
{
    /**
     * @var NodeValueResolver
     */
    private $nodeValueResolver;

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
     * @return Iterator<string[]|String_[]>
     */
    public function provideData(): Iterator
    {
        yield [new String_('value'), 'value'];
    }
}
