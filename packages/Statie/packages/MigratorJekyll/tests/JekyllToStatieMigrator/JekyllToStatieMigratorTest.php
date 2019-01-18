<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Tests\JekyllToStatieMigrator;

use Symplify\Statie\MigratorJekyll\JekyllToStatieMigrator;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class JekyllToStatieMigratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var JekyllToStatieMigrator
     */
    private $jekyllToStatieMigrator;

    protected function setUp()
    {
        $this->jekyllToStatieMigrator = $this->container->get(JekyllToStatieMigrator::class);
    }

    public function test()
    {
        // directory before
        // directory expect

        // 1 cope directory before to the pool
        // process it
        // compare it with directory expected
        // delete it
    }
}