<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

use JsonSerializable;

final class SkipJsonSerializable implements JsonSerializable
{
    public $name;
    public $surname;

    /**
     * @return array<string, string>
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'surname' => $this->surname,
        ];
    }
}
