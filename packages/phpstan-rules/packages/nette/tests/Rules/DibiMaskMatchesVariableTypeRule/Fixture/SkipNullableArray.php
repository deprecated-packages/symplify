<?php

namespace Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class SkipNullableArray
{
    public function run()
    {
        $params = [];
        if ($this->getIds() !== null) {
            $params['i%in'] = $this->getIds();
        }

        return $params;
    }

    public function getIds(): array|null
    {
        return [];
    }
}
