<?php declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Database\Table;

use Nette\Caching\IStorage;
use Nette\Database\Context;
use Nette\Database\IConventions;
use Nette\Database\Table\GroupedSelection;
use Nette\Database\Table\Selection;
use Zenify\NetteDatabaseFilters\Contract\FilterManagerInterface;
use Zenify\NetteDatabaseFilters\Sql\SqlParser;

final class FiltersAwareSelection extends Selection
{
    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    /**
     * @var SqlParser
     */
    private $sqlParser;

    public function __construct(
        FilterManagerInterface $filterManager,
        SqlParser $sqlParser,
        Context $context,
        IConventions $conventions,
        string $tableName,
        IStorage $cacheStorage = null
    ) {
        $this->filterManager = $filterManager;
        $this->sqlParser = $sqlParser;
        parent::__construct($context, $conventions, $tableName, $cacheStorage);
    }

    /**
     * @param string $table
     * @param string $column
     * @param int|NULL $active
     */
    public function getReferencingTable($table, $column, $active = null) : GroupedSelection
    {
        $referencingTable = parent::getReferencingTable($table, $column, $active);

        $this->filterManager->applyFilters($referencingTable, $referencingTable->getName());

        return $referencingTable;
    }

    /**
     * @param string $table
     */
    public function createSelectionInstance($table = null) : Selection
    {
        $selection = parent::createSelectionInstance($table);

        $this->filterManager->applyFilters($selection, $selection->getName());

        return $selection;
    }

    /**
     * @param string $columns
     * @param array ...$params
     */
    public function select($columns, ...$params) : Selection
    {
        $selection = parent::select($columns, ...$params);

        $this->applyFilters($selection);

        return $selection;
    }

    private function applyFilters(Selection $selection) : void
    {
        $tables = $this->sqlParser->parseTablesFromSql($selection->getSql());
        foreach ($tables as $table) {
            $this->filterManager->applyFilters($selection, $table);
        }
    }
}
