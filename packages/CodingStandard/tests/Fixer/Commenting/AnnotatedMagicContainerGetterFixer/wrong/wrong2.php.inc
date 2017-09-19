<?php declare(strict_types=1);

class SomeClass
{
    public function someFunction()
    {
        $someClass = $this->container->get(SomeClass::class);

        $createdService = $this->container->createService(SomeClass::class);
        $this->property = $this->container->get(AnotherClass::class);
    }
}
