<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredAttributeOverAnnotationRule\Fixture;

use Symfony\Component\Routing\Annotation\Route;

final class SomeAnnotatedController
{
    /**
     * @Route()
     */
    public function action()
    {
    }
}
