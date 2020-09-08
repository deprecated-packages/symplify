<?php

class Foo
{
    public function getDefinition(): string
    {
        return <<<PHP
class Definition
{
    public function getResult()
    {
        return ['test' => 'data'];
    }
}
PHP;
    }
}

class Bar
{
    public function getDefinition(): string
    {
        return <<<PHP
class Definition
{
    public function getResult()
    {
        return ['test' => 'data'];
    }
}
PHP;
    }
}