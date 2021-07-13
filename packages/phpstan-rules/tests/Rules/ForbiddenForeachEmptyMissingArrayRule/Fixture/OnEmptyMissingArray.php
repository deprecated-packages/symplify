<?php


namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenForeachEmptyMissingArrayRule\Fixture;

class OnEmptyMissingArray
{
    public function run(array $data)
    {
        foreach ($data ?? [] as $value) {

        }
    }
}
