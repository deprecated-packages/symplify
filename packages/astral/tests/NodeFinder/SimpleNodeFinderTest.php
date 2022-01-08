<?php

declare(strict_types=1);

namespace Symplify\Astral\Tests\NodeFinder;

use Iterator;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use Rector\Core\PhpParser\Parser\SimplePhpParser;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\PhpParser\SmartPhpParser;
use Symplify\Astral\Tests\HttpKernel\AstralKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class SimpleNodeFinderTest extends AbstractKernelTestCase
{
    /**
     * @var Node[]
     */
    private array $nodes = [];

    private SimpleNodeFinder $simpleNodeFinder;

    protected function setUp(): void
    {
        $this->bootKernel(AstralKernel::class);
        $this->simpleNodeFinder = $this->getService(SimpleNodeFinder::class);

        $phpParser = $this->getService(SmartPhpParser::class);
        $this->nodes = $phpParser->parseFile(__DIR__ . '/Source/SomeFile.php.inc');
    }

    public function testFindFirst(): void
    {
        $variable = $this->simpleNodeFinder->findFirst($this->nodes, function(Node $node) {
            return $node instanceof Variable;
        });
        $class = $this->simpleNodeFinder->findFirst($this->nodes, function(Node $node) {
            return $node instanceof Class_;
        });

        $this->assertNotNull($variable);
        $this->assertNotNull($class);

        $this->assertInstanceOf(Variable::class, $variable);
        $this->assertInstanceOf(Class_::class, $class);
    }

}
