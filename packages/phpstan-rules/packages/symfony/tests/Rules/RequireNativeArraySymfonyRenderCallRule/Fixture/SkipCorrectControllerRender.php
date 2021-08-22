<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\RequireNativeArraySymfonyRenderCallRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SkipCorrectControllerRender extends AbstractController
{
    public function default()
    {
        return $this->render('...', [
            'name' => 'John'
        ]);
    }
}
