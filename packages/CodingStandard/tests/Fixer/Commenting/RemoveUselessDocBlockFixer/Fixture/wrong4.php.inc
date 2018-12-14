<?php
namespace SomeNamespace;

class SomeClass
{
    /**
     * @param array         $samples
     *
     * @return array
     */
    public static function getColumnTypes(array $samples) : array
    {
    }

    /**
     * @param int   $column
     * @param array $currentSample
     * @return array
     */
    private function getAxis(int $column, array $currentSample) : array
    {

    }
}

?>
-----
<?php
namespace SomeNamespace;

class SomeClass
{
    public static function getColumnTypes(array $samples) : array
    {
    }

    private function getAxis(int $column, array $currentSample) : array
    {

    }
}

?>
