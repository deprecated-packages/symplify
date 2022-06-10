<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\RequireNativeArraySymfonyRenderCallRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ParameterAsSecondArgument extends AbstractController
{
    public function default()
    {
        $parameters = [];
        $parameters['name'] = 'John';
        $parameters['name'] = 'Doe';

        return $this->render('...', $parameters);
    }
}
