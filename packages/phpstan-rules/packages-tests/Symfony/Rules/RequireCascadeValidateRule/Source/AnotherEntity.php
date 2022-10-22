<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\RequireCascadeValidateRule\Source;

use Symfony\Component\Validator\Constraints as Assert;

final class AnotherEntity
{
    /**
     * @Assert\Valid()
     * @var AnotherPropertyObject
     */
    public $anotherPropertyObject;
}
