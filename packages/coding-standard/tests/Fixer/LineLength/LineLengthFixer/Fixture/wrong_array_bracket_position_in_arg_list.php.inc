<?php

class Bar
{
    public function foo(string $arg1, string $arg2, string $arg3, string $arg4, string $arg5, string $arg6, array $arg7 = []): void
    {
        // Do stuff
    }
}
?>
-----
<?php

class Bar
{
    public function foo(
        string $arg1,
        string $arg2,
        string $arg3,
        string $arg4,
        string $arg5,
        string $arg6,
        array $arg7 = []
    ): void
    {
        // Do stuff
    }
}
?>
