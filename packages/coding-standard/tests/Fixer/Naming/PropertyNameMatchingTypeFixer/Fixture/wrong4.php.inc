<?php

interface SomeClass
{
    public function method(ReflectionProperty $propertyReflection);
}

?>
-----
<?php

interface SomeClass
{
    public function method(ReflectionProperty $reflectionProperty);
}

?>
