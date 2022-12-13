<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Fixture;

use Symfony\Component\Routing\Annotation\Route;

final class SkipControllerMethod
{
    /**
     * @Route()
     */
    public function someAction()
    {
    }

    #[Route]
    public function anotherAction()
    {
    }
}
