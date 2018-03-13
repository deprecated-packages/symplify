<?php declare(strict_types=1);

class SomeClass
{
    public function someFunction() {
        $someLongVariableName = $anotherLongVariableName = $yetAnotherLongVariableName = $andOneMoreLongVariableName = null;

        return function () use ($someLongVariableName, $anotherLongVariableName, $yetAnotherLongVariableName, $andOneMoreLongVariableName) {};

        return function ($argument) use ($someLongVariableName, $anotherLongVariableName, $yetAnotherLongVariableName, $andOneMoreLongVariableName) {};
    }
}
