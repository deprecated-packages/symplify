<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ChangelogLinker\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::REPOSITORY_URL, 'https://github.com/dummy/dummy');

    $parameters->set(Option::NAMES_TO_URLS, [
        'SomeThingToLink' => 'https://someUrl.com',
        # https://github.com/symplify/symplify/issues/1327
        'shopsys/microservice-product-search' => 'https://github.com/shopsys/microservice-product-search',
        'shopsys/microservice-product-search-export' => 'https://github.com/shopsys/microservice-product-search-export',
        'shopsys/microservice-product' => 'https://github.com/shopsys/microservice-product',
    ]);
};
