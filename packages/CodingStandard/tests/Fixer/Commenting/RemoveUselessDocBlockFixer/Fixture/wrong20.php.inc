<?php
namespace SomeNamespace;

class SomeClass
{
    /**
     * @param mixed[] $arrayDiff
     * @return array
     */
    private function addMoreLineNumbers(array $arrayDiff): array
    {

    }
}

?>
-----
<?php
namespace SomeNamespace;

class SomeClass
{
    /**
     * @param mixed[] $arrayDiff
     */
    private function addMoreLineNumbers(array $arrayDiff): array
    {

    }
}

?>
