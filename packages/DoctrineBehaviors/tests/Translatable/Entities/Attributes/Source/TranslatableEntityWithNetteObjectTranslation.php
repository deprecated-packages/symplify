<?php

declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Translatable\Entities\Attributes\Source;

use Knp\DoctrineBehaviors\Model\Translatable\Translation;

final class TranslatableEntityWithNetteObjectTranslation
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


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
