<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Translatable\Entities\Attributes\Source;

use Knp\DoctrineBehaviors\Model\Translatable\Translatable as KnpTranslatable;
use Nette\Object;
use Zenify\DoctrineBehaviors\Entities\Attributes\Translatable as ZenifyTranslatable;

/**
 * @method string getName()
 */
final class TranslatableEntityWithNetteObject extends Object
{
    use KnpTranslatable;
    use ZenifyTranslatable;
}
