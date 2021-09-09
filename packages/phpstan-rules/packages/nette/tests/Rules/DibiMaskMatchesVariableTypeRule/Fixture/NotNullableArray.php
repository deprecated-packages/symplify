<?php

namespace Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class NotNullableArray
{
    public function run()
    {
        $params = [];
        if ($this->getIds() !== null) {
            $params['i%in'] = 'string';
        }

        return $params;
    }

    public function getIds(): array|null
    {
        return [];
    }
}
