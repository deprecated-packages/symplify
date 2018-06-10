<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\DependencyInjection\ContainerFactory;

abstract class AbstractWorkerTestCase extends TestCase
{
    /**
     * @var ChangelogApplication
     */
    private $changelogApplication;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig($this->provideConfig());

        $this->changelogApplication = $container->get(ChangelogApplication::class);
    }

    protected function doProcess(string $originalFile): string
    {
        return $this->changelogApplication->processFile($originalFile);
    }

    abstract protected function provideConfig(): string;
}
