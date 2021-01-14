<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import(__DIR__ . '/../src/Controller', 'annotation');

    $routingConfigurator->add('static_route', '/static')
        ->controller(TemplateController::class)
        ->defaults([
            'template' => 'static.twig',
        ]);

    $routingConfigurator->add('direct_static_route', '/static.html')
        ->controller(TemplateController::class)
        ->defaults([
            'template' => 'static.twig',
        ]);
};
