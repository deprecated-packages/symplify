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
    private $changelogLinker;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig($this->provideConfig());

        $this->changelogLinker = $container->get(ChangelogLinker::class);
    }

    protected function doProcess(string $originalFile): string
    {
        return $this->changelogLinker->processContentWithLinkAppends(file_get_contents($originalFile));
    }

    abstract protected function provideConfig(): string;
}
