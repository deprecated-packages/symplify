<?php

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Tests\Nette\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class InvalidArray
{
    public function run()
    {
        $params = [
            'i%in' => 'string',
        ];

        return $params;
    }
}
