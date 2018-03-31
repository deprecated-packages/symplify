<?php declare(strict_types=1);

class SomeClass
{
    public function someFunction()
    {
        return [
            # form chain method calls
            [__DIR__ . '/NestedMethodCallSource/FormChainMethodCalls.php.inc', 0, 'addRule', ['Stub_Nette\Forms\Rules']],
        ];
    }
}
