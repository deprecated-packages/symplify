<?php declare(strict_types=1);
namespace Zenify\DoctrineExtensionsTree\Tests\Project\Entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Zenify\DoctrineExtensionsTree\Tests\Project\Model\CategoryTree")
 * @Gedmo\Tree(type="materializedPath")
 */
class Category
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @var int
     */
    public $id;

    /**
     * @Gedmo\TreePathSource
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=TRUE)
     * @var Category
     */
    private $parent;

    /**
     * @Gedmo\TreePath(separator="|")
     * @ORM\Column(type="string", nullable=TRUE)
     * @var string
     */
    private $path;

    /**
     * @param string $name
     * @param Category $parent
     */
    public function __construct($name, ?Category $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
