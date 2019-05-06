<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Tests\Configuration;

use Iterator;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Split\Configuration\RepositoryGuard;
use Symplify\MonorepoBuilder\Split\Exception\InvalidRepositoryFormatException;
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
        yield ['git@github.com:Symplify/Symplify.git'];
        yield ['secretToken@github.com:Symplify/Symplify.git'];
        yield ['https://github.com/Symplify/Symplify.git'];
    }

    public function testInvalid(): void
    {
        $this->expectException(InvalidRepositoryFormatException::class);

        $this->repositoryGuard->ensureIsRepository('http://github.com/Symplify/Symplify');
    }
}
