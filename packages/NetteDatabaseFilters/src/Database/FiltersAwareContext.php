<?php

declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Database;

use Nette\Caching\IStorage;
use Nette\Database\Connection;
use Nette\Database\Context;
use Nette\Database\IConventions;
use Nette\Database\IStructure;
use Zenify\NetteDatabaseFilters\Contract\FilterManagerInterface;
use Zenify\NetteDatabaseFilters\Database\Table\FiltersAwareSelection;
use Zenify\NetteDatabaseFilters\Sql\SqlParser;

final class FiltersAwareContext extends Context
{

    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    /**
     * @var IStorage
     */
    private $cacheStorage;

    /**
     * @var SqlParser
     */
    private $sqlParser;


    public function __construct(
        Connection $connection,
        IStructure $structure,
        IConventions $conventions = null,
        IStorage $cacheStorage = null
    ) {
        parent::__construct($connection, $structure, $conventions, $cacheStorage);
        $this->cacheStorage = $cacheStorage;
    }


    public function setFilterManager(FilterManagerInterface $filterManager)
    {
        $this->filterManager = $filterManager;
    }


    public function setSqlParser(SqlParser $sqlParser)
    {
        $this->sqlParser = $sqlParser;
    }


    /**
     * @param string $table
     */
    public function table($table) : FiltersAwareSelection
    {
        $selection = new FiltersAwareSelection(
            $this->filterManager,
            $this->sqlParser,
            $this,
            $this->getConventions(),
            $table,
            $this->cacheStorage
        );

        $this->filterManager->applyFilters($selection, $selection->getName());

        return $selection;
    }
}
