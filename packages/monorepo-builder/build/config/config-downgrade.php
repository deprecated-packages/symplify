<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\DowngradePhp72\Rector\ClassMethod\DowngradeParameterTypeWideningRector;
use Rector\Set\ValueObject\DowngradeSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(DowngradeSetList::PHP_80);
    $containerConfigurator->import(DowngradeSetList::PHP_74);
    $containerConfigurator->import(DowngradeSetList::PHP_73);
    $containerConfigurator->import(DowngradeSetList::PHP_72);

    $services = $containerConfigurator->services();
    $services->set(DowngradeParameterTypeWideningRector::class)
        ->call('configure', [[
            DowngradeParameterTypeWideningRector::SAFE_TYPES => [
                'PHP_CodeSniffer\Sniffs\Sniff',
                \PhpCsFixer\Fixer\FixerInterface::class,
                \Symfony\Component\Console\Output\OutputInterface::class,
                \Symfony\Component\Console\Style\StyleInterface::class,
                // phpstan
                \PhpParser\Parser::class,
                \PhpParser\PrettyPrinterAbstract::class,
            ],
            DowngradeParameterTypeWideningRector::SAFE_TYPES_TO_METHODS => [
                \Symfony\Component\DependencyInjection\ContainerInterface::class => [
                    'setParameter',
                    'getParameter',
                    'hasParameter',
                ],
            ],
        ]]);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SKIP, ['*/Tests/*', '*/tests/*', __DIR__ . '/../../tests']);
};
