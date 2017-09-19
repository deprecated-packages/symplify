<?php declare(strict_types=1);

class SomeClass
{
    public function someFunction()
    {
        /** @var SomeClass $someClass */
        $someClass = $this->container->get(SomeClass::class);
    }
}
