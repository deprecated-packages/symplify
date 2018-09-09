<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\InterdependencyUpdater;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\InterdependencyUpdater;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;

final class InterdependencyUpdaterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var InterdependencyUpdater
     */
    private $interdependencyUpdater;

    protected function setUp(): void
    {
        $this->interdependencyUpdater = $this->container->get(InterdependencyUpdater::class);
    }

    protected function tearDown(): void
    {
        copy(__DIR__ . '/Source/backup-first.json', __DIR__ . '/Source/first.json');
    }

    public function test(): void
    {
        $this->interdependencyUpdater->updateFileInfosWithVendorAndVersion(
            [new SplFileInfo(__DIR__ . '/Source/first.json', 'Source/first.json', 'Source')],
            'symplify',
            '^4.0'
        );

        $this->assertFileEquals(__DIR__ . '/Source/expected-first.json', __DIR__ . '/Source/first.json');
    }
}
