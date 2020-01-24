<?php

declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Tests\JekyllToStatieMigrator;

use Symplify\Statie\Migrator\Tests\AbstractProjectToStatieMigratorTest;
use Symplify\Statie\MigratorJekyll\JekyllToStatieMigrator;

final class JekyllToStatieMigratorTest extends AbstractProjectToStatieMigratorTest
{
    /**
     * @var JekyllToStatieMigrator
     */
    private $jekyllToStatieMigrator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jekyllToStatieMigrator = self::$container->get(JekyllToStatieMigrator::class);
    }

    public function test(): void
    {
        $this->doTestDirectoryBeforeAndAfterMigration(
            $this->jekyllToStatieMigrator,
            __DIR__ . '/Source/Fixture/before',
            __DIR__ . '/Source/Fixture/after'
        );
    }
}
