<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\Tests\Entities\Source;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\Translatable;
use Symplify\DoctrineBehaviors\Entities\Attributes\TranslatableTrait as SymplifyTranslatableTrait;

/**
 * @ORM\Entity
 */
class Category
{
    use Translatable;
    use SymplifyTranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private $id;

    public function __construct(string $name, bool $isActive)
    {
        $this->proxyCurrentLocaleTranslation('setName', [$name]);
        $this->proxyCurrentLocaleTranslation('setIsActive', [$isActive]);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
