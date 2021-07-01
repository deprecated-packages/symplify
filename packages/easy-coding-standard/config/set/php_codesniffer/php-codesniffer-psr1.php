<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs as Generic;
use PHP_CodeSniffer\Standards\PSR1\Sniffs as PSR1;
use PHP_CodeSniffer\Standards\Squiz\Sniffs as Squiz;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // 2. Files

    // 2.1 PHP Tags
    $services->set(Generic\PHP\DisallowAlternativePHPTagsSniff::class);
    $services->set(Generic\PHP\DisallowShortOpenTagSniff::class);

    // 2.2 Character Encoding
    $services->set(Generic\Files\ByteOrderMarkSniff::class);

    // 2.3. Side Effects
    $services->set(PSR1\Files\SideEffectsSniff::class);

    // 3. Namespace and Class Names
    $services->set(PSR1\Classes\ClassDeclarationSniff::class);
    $services->set(Squiz\Classes\ValidClassNameSniff::class);

    // 4. Class Constants, Properties, and Methods

    // 4.1. Constants
    $services->set(Generic\NamingConventions\UpperCaseConstantNameSniff::class);

    // 4.3 Methods
    $services->set(PSR1\Methods\CamelCapsMethodNameSniff::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::SKIP, [
        Generic\PHP\DisallowShortOpenTagSniff::class . 'EchoFound' => null,
    ]);
};
