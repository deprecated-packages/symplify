<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanPHPConfig\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(
        OPTION::IGNORE_ERRORS,
        [
            '#Call to an undefined method Symfony\\Component\\Config\\Definition\\Builder\\NodeDefinition\\:\\:children\\(\\)#',
            '#Call to an undefined method Symfony\Component\Config\Definition\Builder\NodeDefinition\:\:children\(\)#',
            '#Call to an undefined method Symfony\\Component\\Config\\Definition\\Builder\\NodeDefinition\:\:children\(\)#'
        ]
    );
};

?>
-----
parameters:
    ignoreErrors:
        - "#Call to an undefined method Symfony\\Component\\Config\\Definition\\Builder\\NodeDefinition\\:\\:children\\(\\)#"
        - "#Call to an undefined method Symfony\\Component\\Config\\Definition\\Builder\\NodeDefinition\\:\\:children\\(\\)#"
        - "#Call to an undefined method Symfony\\Component\\Config\\Definition\\Builder\\NodeDefinition\\:\\:children\\(\\)#"
