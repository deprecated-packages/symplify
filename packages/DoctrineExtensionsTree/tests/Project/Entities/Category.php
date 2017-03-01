<?php declare(strict_types=1);
namespace Symplify\DoctrineExtensionsTree\Tests\Project\Entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Symplify\DoctrineExtensionsTree\Tests\Project\Model\CategoryTree")
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

    public function __construct(string $name, ?Category $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParent(): Category
    {
        return $this->parent;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
