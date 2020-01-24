<?php

final class SomeClass
{
    public function method(ReflectionProperty $propertyReflection)
    {
        if ($propertyReflection) {
        }

        if ($propertyReflection->getDocComment()) {
        }
    }
}

?>
-----
<?php

final class SomeClass
{
    public function method(ReflectionProperty $reflectionProperty)
    {
        if ($reflectionProperty) {
        }

        if ($reflectionProperty->getDocComment()) {
        }
    }
}

?>
