<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\InvokableControllerByRouteNamingRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SkipRandomPublicMethodController extends AbstractController
{
    public function run()
    {
    }
}
