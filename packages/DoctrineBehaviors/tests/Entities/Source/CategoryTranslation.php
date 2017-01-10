<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Entities\Source;

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


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }


    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive)
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


    /**
     * @param Tag $tag
     * @return bool
     */
    public function hasTag(Tag $tag)
    {
        return $this->tags->contains($tag);
    }


    public function addTag(Tag $tag)
    {
        $this->tags->add($tag);
    }


    /**
     * @return bool
     */
    public function shouldRenderSubcategories()
    {
        return $this->shouldRenderSubcategories;
    }


    /**
     * @param bool $shouldRenderSubcategories
     */
    public function setShouldRenderSubcategories($shouldRenderSubcategories)
    {
        $this->shouldRenderSubcategories = $shouldRenderSubcategories;
    }
}
