<?php declare(strict_types=1);

class SomeClass
{
    public function someMethod()
    {
        $this->labels = [1 => $this->labels[0], -1 => $this->labels[1]];
    }
}
