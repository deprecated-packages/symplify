<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\Tests\MissingSkipPrefixResolver;

use Symplify\EasyTesting\Finder\FixtureFinder;
use Symplify\EasyTesting\HttpKernel\EasyTestingKernel;
use Symplify\EasyTesting\MissplacedSkipPrefixResolver;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class MissingSkipPrefixResolverTest extends AbstractKernelTestCase
{
    /**
     * @var MissplacedSkipPrefixResolver
     */
    private $missingSkipPrefixResolver;

    /**
     * @var FixtureFinder
     */
    private $fixtureFinder;

    protected function setUp(): void
    {
        $this->bootKernel(EasyTestingKernel::class);
        $this->missingSkipPrefixResolver = $this->getService(MissplacedSkipPrefixResolver::class);
        $this->fixtureFinder = $this->getService(FixtureFinder::class);
    }

    public function test(): void
    {
        $fileInfos = $this->fixtureFinder->find([__DIR__ . '/Fixture']);
        $invalidFileInfos = $this->missingSkipPrefixResolver->resolve($fileInfos);

        $this->assertCount(2, $invalidFileInfos);
    }
}
