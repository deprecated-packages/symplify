<?php declare(strict_types=1);

class SomeClass
{
    public function __construct(PHPStanNodeScopeResolver $random)
    {
        $this->random = $random;
    }
}
