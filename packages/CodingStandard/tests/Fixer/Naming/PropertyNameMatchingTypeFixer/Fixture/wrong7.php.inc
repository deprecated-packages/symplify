<?php

class SomeClass
{
    public function __construct(PHPStanNodeScopeResolver $random)
    {
        $this->random = $random;
    }
}

?>
-----
<?php

class SomeClass
{
    public function __construct(PHPStanNodeScopeResolver $phpStanNodeScopeResolver)
    {
        $this->random = $phpStanNodeScopeResolver;
    }
}

?>
