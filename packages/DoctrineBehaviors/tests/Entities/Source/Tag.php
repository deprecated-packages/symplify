<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Entities\Source;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="CategoryTranslation", inversedBy="tags", cascade={"persist"})
     * @var CategoryTranslation[]|ArrayCollection
     */
    private $categories;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->categories = new ArrayCollection;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
}
