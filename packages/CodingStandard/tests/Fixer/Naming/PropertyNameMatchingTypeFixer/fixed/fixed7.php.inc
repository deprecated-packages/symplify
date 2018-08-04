<?php declare(strict_types=1);

class SomeClass
{
    public function __construct(PHPStanNodeScopeResolver $phpStanNodeScopeResolver)
    {
        $this->random = $phpStanNodeScopeResolver;
    }
}
