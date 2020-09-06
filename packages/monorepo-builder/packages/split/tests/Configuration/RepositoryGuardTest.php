<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Tests\Configuration;

use Iterator;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Split\Configuration\RepositoryGuard;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class RepositoryGuardTest extends AbstractKernelTestCase
{
    /**
     * @var RepositoryGuard
     */
    private $repositoryGuard;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->repositoryGuard = self::$container->get(RepositoryGuard::class);
    }

    /**
     * @dataProvider provideDataForEnsureIsRepository()
     * @doesNotPerformAssertions
     */
    public function testValid(string $repository): void
    {
        $this->repositoryGuard->ensureIsRepository($repository);
    }

    public function provideDataForEnsureIsRepository(): Iterator
    {
        yield ['.git'];
        yield ['git@github.com:symplify/symplify.git'];
        yield ['secretToken@github.com:symplify/symplify.git'];
        yield ['https://github.com/symplify/symplify.git'];
        yield ['AUTHTOKEN@ssh.dev.azure.com:v3/username/symplify/symplify'];
        yield ['https://AUTHTOKEN@dev.azure.com/username/symplify/_git/symplify'];
        yield ['file:///home/developer/git/project.git'];
    }
}
