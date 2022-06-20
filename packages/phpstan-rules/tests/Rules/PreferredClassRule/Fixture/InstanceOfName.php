<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredClassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\PreferredClassRule\Source\AbstractNotWhatYouWant;

final class InstanceOfName
{
    public function run($node)
    {
        if ($node instanceof AbstractNotWhatYouWant) {
            return true;
        }

        return false;
    }
}
