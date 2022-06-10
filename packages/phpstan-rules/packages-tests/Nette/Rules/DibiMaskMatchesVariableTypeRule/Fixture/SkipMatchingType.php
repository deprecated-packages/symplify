<?php

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class SkipMatchingType
{
    public function run(\Dibi\Connection $connection)
    {
        $arr = [
            'a' => 'hello',
            'b'  => true,
        ];

        $connection->query('INSERT INTO table %v', $arr);
    }
}
