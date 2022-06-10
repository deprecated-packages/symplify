<?php

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class SkipMaskNonDibi
{
    public function run(\Dibi\Connection $connection)
    {
        $string = 'hello';

        $connection->query('INSERT INTO table %ve', $string);
    }
}
