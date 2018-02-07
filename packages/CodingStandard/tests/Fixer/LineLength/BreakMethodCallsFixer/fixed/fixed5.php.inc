<?php declare(strict_types=1);

class SomeClass
{
    public function someFunction()
    {
        if (something) {
            throw new SomeException(sprintf(
                'SuperLongMethod',
                'SuperLongMethod',
                'SuperLongMethod',
                'SuperLongMethod'
            ));
        }
    }
}
