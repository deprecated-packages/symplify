<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorSculpin\Tests\SculpinToStatieMigrator;

use Symplify\Statie\Migrator\Tests\AbstractProjectToStatieMigratorTest;
use Symplify\Statie\MigratorSculpin\SculpinToStatieMigrator;

final class SculpinToStatieMigratorTest extends AbstractProjectToStatieMigratorTest
{
    /**
     * @var SculpinToStatieMigrator
     */
    private $sculpinToStatieMigrator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sculpinToStatieMigrator = $this->container->get(SculpinToStatieMigrator::class);
    }

    public function test(): void
    {
        $this->doTestDirectoryBeforeAndAfterMigration(
            $this->sculpinToStatieMigrator,
            __DIR__ . '/Source/Fixture/before',
            __DIR__ . '/Source/Fixture/after'
        );
    }
}
