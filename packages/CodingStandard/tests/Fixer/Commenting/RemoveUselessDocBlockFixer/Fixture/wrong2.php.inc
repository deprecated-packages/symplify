<?php
namespace SomeNamespace;

class SomeClass
{
    /**
     * @param array $arrayDiff
     * @return mixed[]
     */
    private function addLineNumbers(array $arrayDiff): array
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
     * @return mixed[]
     */
    private function addLineNumbers(array $arrayDiff): array
    {

    }
}

?>
