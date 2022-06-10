<?php

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class InvalidType
{
    public function run(\Dibi\Connection $connection)
    {
        $string = 'hello';

        $connection->query('INSERT INTO table %v', $string);
    }
}
