# DoctrineExtensions - Tree

[![Build Status](https://img.shields.io/travis/Zenify/DoctrineExtensionsTree.svg?style=flat-square)](https://travis-ci.org/Zenify/DoctrineExtensionsTree)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Zenify/DoctrineExtensionsTree.svg?style=flat-square)](https://scrutinizer-ci.com/g/Zenify/DoctrineExtensionsTree)
[![Downloads](https://img.shields.io/packagist/dt/zenify/doctrine-extensions-tree.svg?style=flat-square)](https://packagist.org/packages/zenify/doctrine-extensions-tree)

Implementation of [Tree from DoctrineExtensions](https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/tree.md) to Nette.


## Install

```sh
composer require zenify/doctrine-extensions-tree
```

Register extension:

```yaml
# app/config/config.neon
extensions:
	- Zenify\DoctrineExtensionsTree\DI\TreeExtension
```


## Usage

For entity implementation, follow [manual](https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/tree.md)
 
Very simple entity could look like this:
 
```php
namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Model\CategoryTree")
 * @Gedmo\Tree(type="materializedPath")
 */
class Category
{

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue
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
	public function __construct($name, Category $parent = NULL)
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
```

And it's repository:

```php
namespace App\Model;

use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;


class CategoryTree extends MaterializedPathRepository
{

}
```


## Testing

```bash
composer check-cs # see "scripts" section of composer.json for more details 
vendor/bin/phpunit
```


## Contributing

Rules are simple:

- new feature needs tests
- all tests must pass
- 1 feature per PR

We would be happy to merge your feature then!
