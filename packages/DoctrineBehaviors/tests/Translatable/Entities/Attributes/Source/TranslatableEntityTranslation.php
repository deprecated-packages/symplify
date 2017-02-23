<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Translatable\Entities\Attributes\Source;

use Knp\DoctrineBehaviors\Model\Translatable\Translation;

final class TranslatableEntityTranslation
{
    use Translation;

    /**
     * @var string
     */
    private $name = 'someName';

    /**
     * @var int
     */
    private $position = 5;

    public function getName(): string
    {
        return $this->name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
