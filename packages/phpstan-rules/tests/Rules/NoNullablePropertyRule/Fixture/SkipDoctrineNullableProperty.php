<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNullablePropertyRule\Fixture;

use DateTime;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
final class SkipDoctrineNullableProperty
{
    private ?DateTime $value = null;
}
