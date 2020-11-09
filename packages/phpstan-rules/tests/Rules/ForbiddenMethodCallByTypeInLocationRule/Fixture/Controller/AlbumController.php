<?php

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallByTypeInLocationRule\Fixture\Controller;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallByTypeInLocationRule\Fixture\View\Helper\NumberHelper;

class AlbumController
{
    public function get()
    {
        $helper = new NumberHelper();
        $helper->get();
    }
}
