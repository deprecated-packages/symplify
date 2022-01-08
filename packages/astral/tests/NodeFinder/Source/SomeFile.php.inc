<?php

class SomeClass
{
    public function someAction($param)
    {
        $variable = 5;

        if ($param == 'x') {
            echo 'hello';
        }
        $z = 3;

        $this->anotherAction();
    }

    public function anotherAction() {

    }
}
