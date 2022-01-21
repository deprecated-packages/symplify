<?php

declare(strict_types=1);

use Composer\Semver\Semver;

use Composer\Semver\VersionParser;
use Nette\Neon\Decoder;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\ActiveClass\Command\CheckActiveClassCommand;
use Symplify\EasyCI\Command\CheckCommentedCodeCommand;
use Symplify\EasyCI\Command\CheckConflictsCommand;
use Symplify\EasyCI\Command\CheckLatteTemplateCommand;
use Symplify\EasyCI\Command\CheckTwigRenderCommand;
use Symplify\EasyCI\Command\CheckTwigTemplateCommand;
use Symplify\EasyCI\Command\PhpVersionsJsonCommand;
use Symplify\EasyCI\Command\ValidateFileLengthCommand;
use Symplify\EasyCI\Config\Command\CheckConfigCommand;
use Symplify\EasyCI\Neon\Command\CheckNeonCommand;
use Symplify\EasyCI\Psr4\Command\CheckFileClassNameCommand;
use Symplify\EasyCI\Psr4\Command\FindMultiClassesCommand;
use Symplify\EasyCI\Psr4\Command\GeneratePsr4ToPathsCommand;
use Symplify\EasyCI\StaticDetector\Command\DetectStaticCommand;
use Symplify\EasyCI\Testing\Command\DetectUnitTestsCommand;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/config-packages.php');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\EasyCI\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);

    // console
    $services->set(Application::class)
        ->call('addCommands', [[
            // basic commands
            service(CheckCommentedCodeCommand::class),
            service(CheckConflictsCommand::class),
            service(CheckLatteTemplateCommand::class),
            service(CheckTwigRenderCommand::class),
            service(CheckTwigTemplateCommand::class),
            service(PhpVersionsJsonCommand::class),
            service(ValidateFileLengthCommand::class),
            // package commands
            service(CheckActiveClassCommand::class),
            service(CheckConfigCommand::class),
            service(CheckNeonCommand::class),
            service(CheckFileClassNameCommand::class),
            service(FindMultiClassesCommand::class),
            service(GeneratePsr4ToPathsCommand::class),
            service(DetectStaticCommand::class),
            service(DetectUnitTestsCommand::class),
        ]]);

    $services->set(VersionParser::class);
    $services->set(Semver::class);

    // neon
    $services->set(Decoder::class);

    // php-parser
    $services->set(ParserFactory::class);
    $services->set(Parser::class)
        ->factory([service(ParserFactory::class), 'create'])
        ->args([ParserFactory::PREFER_PHP7]);

    $services->set(Standard::class);
    $services->set(NodeFinder::class);
    $services->set(ClassLikeExistenceChecker::class);
};
