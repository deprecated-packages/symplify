<?php declare(strict_types=1);

/**
 * Some comment
 */
class SomeClass
{

    /**
     * Some comment
     */
    public function go()
    {
        /** @var SomeClass $service */
        $service = $this->container->getService();
    }
}
