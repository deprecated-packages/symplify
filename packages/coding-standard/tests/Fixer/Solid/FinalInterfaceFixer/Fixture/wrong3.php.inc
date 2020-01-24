<?php

namespace SomeNamespace;


class SomeClass implements \SomeInterface
{

}

class AlsoSomeClass implements SomeInterface
{

}

class AnotherClass implements \YetAnotherInterface
{

}

?>
-----
<?php

namespace SomeNamespace;


final class SomeClass implements \SomeInterface
{

}

class AlsoSomeClass implements SomeInterface
{

}

class AnotherClass implements \YetAnotherInterface
{

}

?>
