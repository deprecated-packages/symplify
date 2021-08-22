<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\RequireNativeArraySymfonyRenderCallRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SkipNoSecondArgument extends AbstractController
{
    public function default()
    {
        return $this->render('...');
    }
}
