<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ChangelogLinker\ValueObject\ChangelogFormat;
use Symplify\ChangelogLinker\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::CHANGELOG_FORMAT, ChangelogFormat::PACKAGES_THEN_CATEGORIES);

    $parameters->set(Option::AUTHORS_TO_IGNORE, ['TomasVotruba']);

    $parameters->set(Option::PACKAGE_ALIASES, [
        'CS' => 'CodingStandard',
        'PB' => 'PackageBuilder',
        'MB' => 'MonorepoBuilder',
        'ECS' => 'EasyCodingStandard',
        'ECST' => 'EasyCodingStandardTester',
        'CL' => 'ChangelogLinker',
        'LT' => 'LatteToTwigConverter',
        'SSD' => 'SymfonyStaticDumper',
        'EH' => 'EasyHydrator',
    ]);
};
