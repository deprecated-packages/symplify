<?php

class SomeClass
{
    public function someFunction()
    {
        if (something) {
            throw new SomeException(sprintf('SuperLongMethod', 'SuperLongMethod', 'SuperLongMethod', 'SuperLongMethod'));
        }
    }
}

?>
-----
<?php

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

?>
