<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanPHPConfig\ValueObject\Level;
use Symplify\PHPStanPHPConfig\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::LEVEL, Level::LEVEL_5);
};

?>
-----
parameters:
    level: 5
