<?php

declare(strict_types=1);

namespace Symplify\Astral\Tests\NodeValue;

use Iterator;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\TestCase;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\Astral\StaticFactory\SimpleNameResolverStaticFactory;
use Symplify\Astral\Tests\NodeValue\Source\FakeScope;
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
        $this->nodeValueResolver = new NodeValueResolver($simpleNameResolver, new TypeChecker());
    }

    /**
     * @dataProvider provideData()
     * @param mixed $expectedValue
     */
    public function test(Expr $expr, $expectedValue): void
    {
        $fakeScope = new FakeScope();
        $resolvedValue = $this->nodeValueResolver->resolve($expr, $fakeScope);
        $this->assertSame($expectedValue, $resolvedValue);
    }

    public function provideData(): Iterator
    {
        yield [new String_('value'), 'value'];
    }
}
