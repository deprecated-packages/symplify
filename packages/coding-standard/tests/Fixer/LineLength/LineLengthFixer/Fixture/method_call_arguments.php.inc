<?php

class SomeClass
{
    public function someFunction()
    {
        $this->someFunctionCall($someLoooooooooooooongArgument, $anotherLoooooooooooooooooooooooooooooooooooongArgumentTo);

        $this->someOtherFunctionCall(
            $superShort,
            $anotherShort
        );
    }
}

?>
-----
<?php

class SomeClass
{
    public function someFunction()
    {
        $this->someFunctionCall(
            $someLoooooooooooooongArgument,
            $anotherLoooooooooooooooooooooooooooooooooooongArgumentTo
        );

        $this->someOtherFunctionCall($superShort, $anotherShort);
    }
}

?>
