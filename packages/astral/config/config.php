<?php

declare(strict_types=1);

use PhpParser\ConstExprEvaluator;
use PhpParser\NodeFinder;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\Astral\PhpParser\SmartPhpParser;
use Symplify\Astral\PhpParser\SmartPhpParserFactory;
use Symplify\PackageBuilder\Php\TypeChecker;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->public();

    $services->load('Symplify\Astral\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/StaticFactory',
            __DIR__ . '/../src/ValueObject',
            __DIR__ . '/../src/NodeVisitor',
            __DIR__ . '/../src/PhpParser/SmartPhpParser.php',
            __DIR__ . '/../src/PhpDocParser/PhpDocNodeVisitor/CallablePhpDocNodeVisitor.php',
        ]);

    $services->set(SmartPhpParser::class)
        ->factory([service(SmartPhpParserFactory::class), 'create']);

    $services->set(ConstExprEvaluator::class);
    $services->set(TypeChecker::class);
    $services->set(NodeFinder::class);

    // phpdoc parser
    $services->set(PhpDocParser::class);
    $services->set(Lexer::class);
    $services->set(TypeParser::class);
    $services->set(ConstExprParser::class);
};
