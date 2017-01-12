<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests\Source\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
 */
class Product
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isActive = true;

    public function __construct(bool $isActive)
    {
        $this->isActive = $isActive;
    }

    public function isActive() : bool
    {
        return $this->isActive;
    }
}
