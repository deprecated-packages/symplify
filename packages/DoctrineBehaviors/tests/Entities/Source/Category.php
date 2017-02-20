<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Entities\Source;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\Translatable;
use Zenify\DoctrineBehaviors\Entities\Attributes\TranslatableTrait as ZenifyTranslatableTrait;

/**
 * @ORM\Entity
 */
class Category
{
    use Translatable;
    use ZenifyTranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private $id;

    /**
     * @param string $name
     * @param bool $isActive
     */
    public function __construct($name, $isActive)
    {
        $this->proxyCurrentLocaleTranslation('setName', [$name]);
        $this->proxyCurrentLocaleTranslation('setIsActive', [$isActive]);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
