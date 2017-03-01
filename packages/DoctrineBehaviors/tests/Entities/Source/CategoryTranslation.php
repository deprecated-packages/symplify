<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\Tests\Entities\Source;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\Translation;

/**
 * @ORM\Entity
 */
class CategoryTranslation
{
    use Translation;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="categories", cascade={"persist"})
     * @var Tag[]|ArrayCollection
     */
    private $tags;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $shouldRenderSubcategories;

    public function __construct()
    {
        $this->tags = new ArrayCollection;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return Tag[]|ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function hasTag(Tag $tag): bool
    {
        return $this->tags->contains($tag);
    }

    public function addTag(Tag $tag): void
    {
        $this->tags->add($tag);
    }

    public function shouldRenderSubcategories(): bool
    {
        return $this->shouldRenderSubcategories;
    }

    public function setShouldRenderSubcategories(bool $shouldRenderSubcategories): void
    {
        $this->shouldRenderSubcategories = $shouldRenderSubcategories;
    }
}
