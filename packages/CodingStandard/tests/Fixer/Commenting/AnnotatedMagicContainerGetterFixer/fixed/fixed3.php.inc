<?php declare(strict_types=1);

class SomeClass
{
    public function someFunction()
    {

        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
    }
}
