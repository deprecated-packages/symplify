<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\DowngradePhp72\Rector\ClassMethod\DowngradeParameterTypeWideningRector;
use Rector\DowngradePhp80\Rector\Class_\DowngradeAttributeToAnnotationRector;
use Rector\DowngradePhp80\ValueObject\DowngradeAttributeToAnnotation;
use Rector\Set\ValueObject\DowngradeSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Contracts\Service\Attribute\Required;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(DowngradeSetList::PHP_80);
    $containerConfigurator->import(DowngradeSetList::PHP_74);
    $containerConfigurator->import(DowngradeSetList::PHP_73);
    $containerConfigurator->import(DowngradeSetList::PHP_72);
    $containerConfigurator->import(DowngradeSetList::PHP_71);
    // currently breaks - https://github.com/symplify/easy-coding-standard/runs/2603926642
    // $containerConfigurator->import(DowngradeSetList::PHP_70);

    $services = $containerConfigurator->services();

    $services->set(DowngradeParameterTypeWideningRector::class)
        ->call('configure', [[
            DowngradeParameterTypeWideningRector::SAFE_TYPES => [
                'PHP_CodeSniffer\Sniffs\Sniff',
                \PhpCsFixer\Fixer\FixerInterface::class,
                \Symfony\Component\Console\Output\OutputInterface::class,
                \Symfony\Component\Console\Style\StyleInterface::class,
                \Symplify\SimplePhpDocParser\Contract\PhpDocNodeVisitorInterface::class,
                // phpstan
                \PhpParser\Parser::class,
                \Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface::class,
                \Symplify\RuleDocGenerator\Contract\Category\CategoryInfererInterface::class,
                \PhpParser\PrettyPrinterAbstract::class,
            ],
        ]]);

    $services->set(DowngradeAttributeToAnnotationRector::class)
        ->call('configure', [[
            DowngradeAttributeToAnnotationRector::ATTRIBUTE_TO_ANNOTATION => ValueObjectInliner::inline([
                new DowngradeAttributeToAnnotation(Required::class, 'required'),
            ]),
        ]]);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SKIP, ['*/Tests/*', '*/tests/*', __DIR__ . '/../../tests']);
};
