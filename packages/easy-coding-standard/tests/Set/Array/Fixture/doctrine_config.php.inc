<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', ['dbal' => ['url' => '%env(resolve:DATABASE_URL)%', 'charset' => 'utf8mb4', 'default_table_options' => ['collate' => 'utf8mb4_unicode_ci']], 'orm' => ['auto_generate_proxy_classes' => true, 'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware', 'auto_mapping' => true, 'mappings' => ['App' => ['is_bundle' => false, 'type' => 'annotation', 'dir' => '%kernel.project_dir%/src/Entity', 'prefix' => 'App\Entity', 'alias' => 'App']]]]);
};

?>
-----
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(resolve:DATABASE_URL)%',
            'charset' => 'utf8mb4',
            'default_table_options' => [
                'collate' => 'utf8mb4_unicode_ci',
            ],
        ],
        'orm' => [
            'auto_generate_proxy_classes' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'auto_mapping' => true,
            'mappings' => [
                'App' => [
                    'is_bundle' => false,
                    'type' => 'annotation',
                    'dir' => '%kernel.project_dir%/src/Entity',
                    'prefix' => 'App\Entity',
                    'alias' => 'App',
                ],
            ],
        ],
    ]);
};

?>
