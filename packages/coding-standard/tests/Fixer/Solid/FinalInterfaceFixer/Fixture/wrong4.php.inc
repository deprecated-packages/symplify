<?php

namespace SomeNamespace\AnonymousClass;


class SomeClass implements SomeSpecialInterface
{
    public function createAnonymousClass()
    {
        return new class
        {
        };
    }

    public function createAnonymousClassImplementsInterface(): SomeInterface
    {
        return new class implements SomeInterface
        {
        };
    }
}

?>
-----
<?php

namespace SomeNamespace\AnonymousClass;


final class SomeClass implements SomeSpecialInterface
{
    public function createAnonymousClass()
    {
        return new class
        {
        };
    }

    public function createAnonymousClassImplementsInterface(): SomeInterface
    {
        return new class implements SomeInterface
        {
        };
    }
}

?>
