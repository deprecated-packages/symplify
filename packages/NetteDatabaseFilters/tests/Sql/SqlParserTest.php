<?php declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Tests\Sql;

use PHPSQLParser\PHPSQLParser;
use PHPUnit\Framework\TestCase;
use Zenify\NetteDatabaseFilters\Sql\SqlParser;

final class SqlParserTest extends TestCase
{
    /**
     * @var SqlParser
     */
    private $sqlParser;

    protected function setUp()
    {
        $this->sqlParser = new SqlParser(new PHPSQLParser);
    }

    public function testParseTables()
    {
        $tables = $this->sqlParser->parseTablesFromSql(
            'SELECT [comment].* FROM [article] LEFT JOIN [comment] ON [article].[id] '
            . '= [comment].[article_id] WHERE ([article].[id] != ?)'
        );

        $this->assertSame(['article', 'comment'], $tables);
    }
}
