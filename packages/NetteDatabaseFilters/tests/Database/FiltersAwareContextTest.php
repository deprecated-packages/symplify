<?php declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Tests\Database;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use PHPUnit\Framework\TestCase;
use Zenify\NetteDatabaseFilters\Database\Table\FiltersAwareSelection;
use Zenify\NetteDatabaseFilters\Tests\ContainerFactory;

final class FiltersAwareContextTest extends TestCase
{

    /**
     * @var Selection
     */
    private $selection;


    protected function setUp()
    {
        $container = (new ContainerFactory)->create();

        /** @var Context $database */
        $database = $container->getByType(Context::class);
        $this->selection = $database->table('comment');
    }


    public function testFetchAll()
    {
        $this->assertInstanceOf(FiltersAwareSelection::class, $this->selection);

        $result = $this->selection->fetchAll();

        $this->assertCount(50, $result);
    }


    public function testGet()
    {
        $this->assertInstanceOf(ActiveRow::class, $this->selection->get(2));
        $this->assertFalse((bool) $this->selection->get(1));
    }


    public function testFetchPairs()
    {
        $pairs = $this->selection->fetchPairs('id', 'name');

        $this->assertCount(50, $pairs);
    }


    public function testFetchIteration()
    {
        $userCount = 0;
        foreach ($this->selection as $comment) {
            $userCount++;
        }
        $this->assertSame(50, $userCount);
    }


    public function testFetch()
    {
        for ($i = 0; $i < 50; $i++) {
            $comment = $this->selection->fetch();
            $this->assertInstanceOf(ActiveRow::class, $comment);
        }

        $commentOver = $this->selection->fetch();
        $this->assertFalse($commentOver);
    }


    public function testCount()
    {
        $this->assertSame(50, $this->selection->count());
    }


    public function testWhere()
    {
        $this->selection->where('name != ?', 'Jan');
        $this->assertSame(48, $this->selection->count());
    }
}
