<?php

class Foo
{
    protected function bar()
    {
        return new class() implements ResponseInterface {
            public function baz()
            {
                return [];
            }
        };
    }
}
