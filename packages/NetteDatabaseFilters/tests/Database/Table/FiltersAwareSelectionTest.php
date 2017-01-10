<?php

declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Tests\Database\Table;

use Nette\Database\Context;
use PHPUnit\Framework\TestCase;
use Zenify\NetteDatabaseFilters\Tests\ContainerFactory;

final class FiltersAwareSelectionTest extends TestCase
{

    /**
     * @var Context
     */
    private $database;


    protected function setUp()
    {
        $container = (new ContainerFactory)->create();

        $this->database = $container->getByType(Context::class);
    }


    public function testRelated()
    {
        $article = $this->database->table('article')
            ->get(2);

        $this->assertCount(4, $article->related('comment'));
    }


    public function testReferenced()
    {
        $comment = $this->database->table('comment')
            ->get(31);

        $article = $comment->ref('article');
        $this->assertNull($article);
    }


    public function testSelect()
    {
        $commentsOfAllUsers = $this->database->table('article')
            ->select(':comment.*');

        $this->assertCount(47, $commentsOfAllUsers);
    }
}
