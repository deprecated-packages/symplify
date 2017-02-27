<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Translatable\Entities\Attributes\Source;

use Knp\DoctrineBehaviors\Model\Translatable\Translatable as KnpTranslatable;
use Zenify\DoctrineBehaviors\Entities\Attributes\TranslatableTrait as ZenifyTranslatableTrait;

/**
 * @method string getName()
 */
final class TranslatableEntity
{
    use KnpTranslatable;
    use ZenifyTranslatableTrait;
}
