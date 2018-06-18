<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\DependencyInjection\ContainerFactory;

abstract class AbstractWorkerTestCase extends TestCase
{
    /**
     * @var ChangelogLinker
     */
    private $changelogApplication;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig($this->provideConfig());

        $this->changelogApplication = $container->get(ChangelogLinker::class);
    }

    protected function doProcess(string $originalFile): string
    {
        return $this->changelogApplication->processContent(file_get_contents($originalFile));
    }

    abstract protected function provideConfig(): string;
}
