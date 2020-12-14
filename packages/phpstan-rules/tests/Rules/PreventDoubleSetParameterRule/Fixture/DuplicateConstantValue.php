<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\PreventDoubleSetParameterRule\Source\OptionConstants;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(OptionConstants::NAME, 'b');
    $parameters->set(OptionConstants::NAME, 'c');
};
