<?php

namespace SomeNamespaceWithEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class SomeItem
{
    private $status;

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}

$someItem = new SomeItem();
