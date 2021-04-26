<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Source\Album;
use Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Source\Track;

final class DynamicPropertyCall
{
    public function run($node)
    {
        if ($node instanceof Album || $node instanceof Track) {
            return $node::TITLE;
        }
    }
}
