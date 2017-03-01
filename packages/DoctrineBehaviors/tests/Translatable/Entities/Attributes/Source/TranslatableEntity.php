<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\Tests\Translatable\Entities\Attributes\Source;

use Knp\DoctrineBehaviors\Model\Translatable\Translatable as KnpTranslatable;
use Symplify\DoctrineBehaviors\Entities\Attributes\TranslatableTrait as SymplifyTranslatableTrait;

/**
 * @method string getName()
 */
final class TranslatableEntity
{
    use KnpTranslatable;
    use SymplifyTranslatableTrait;
}
