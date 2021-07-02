<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Tests\NodeFactory;

use PhpParser\Node\Expr\ConstFetch;
use PHPUnit\Framework\TestCase;
use Symplify\ConfigTransformer\Provider\YamlContentProvider;
use Symplify\PhpConfigPrinter\NodeFactory\ConstantNodeFactory;

final class ConstantNodeFactoryTest extends TestCase
{
    private ConstantNodeFactory $constantNodeFactory;

    private YamlContentProvider $yamlContentProvider;

    protected function setUp(): void
    {
        $this->yamlContentProvider = new YamlContentProvider();
        $this->constantNodeFactory = new ConstantNodeFactory($this->yamlContentProvider);
    }

    public function testThatDeprecatedPHPConstantExists(): void
    {
        $previousLevel = error_reporting(E_ALL & ~E_DEPRECATED);

        $value = constant('PGSQL_LIBPQ_VERSION_STR');

        $this->assertNotEmpty($value);

        error_reporting($previousLevel);
    }

    public function testConstantFetchNode(): void
    {
        $this->yamlContentProvider->setContent(
            <<<CODE_SAMPLE
            services:
                My\Service:
                    arguments:
                        - !php/const PHP_VERSION
            CODE_SAMPLE
        );

        $previousLevel = error_reporting(E_ALL);

        $constFetch = $this->constantNodeFactory->createConstantIfValue(PHP_VERSION);
        $this->assertInstanceOf(ConstFetch::class, $constFetch);
        /** @var ConstFetch $constFetch */
        $this->assertSame($constFetch->name->toString(), 'PHP_VERSION');

        error_reporting($previousLevel);
    }
}
