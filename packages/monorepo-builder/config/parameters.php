<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('env(GITHUB_TOKEN)', null);
    $parameters->set(Option::GITHUB_TOKEN, '%env(GITHUB_TOKEN)%');
    $parameters->set(Option::PACKAGE_DIRECTORIES, [getcwd() . '/packages']);
    $parameters->set(Option::PACKAGE_DIRECTORIES_EXCLUDES, []);
    $parameters->set(Option::DATA_TO_APPEND, []);
    $parameters->set(Option::DATA_TO_REMOVE, []);

    $parameters->set(Option::EXCLUDE_PACKAGE_VERSION_CONFLICTS, []);

    $parameters->set(Option::IS_STAGE_REQUIRED, false);
    $parameters->set(Option::STAGES_TO_ALLOW_EXISTING_TAG, []);

    // for back compatibility, better switch to "main"
    $parameters->set(Option::DEFAULT_BRANCH_NAME, 'master');

    $parameters->set(Option::ROOT_DIRECTORY, getcwd());
    $parameters->set(Option::PACKAGE_ALIAS_FORMAT, '<major>.<minor>-dev');
    $parameters->set(Option::INLINE_SECTIONS, ['keywords']);

    $parameters->set(Option::SECTION_ORDER, [
        ComposerJsonSection::NAME,
        ComposerJsonSection::TYPE,
        ComposerJsonSection::DESCRIPTION,
        ComposerJsonSection::KEYWORDS,
        ComposerJsonSection::HOMEPAGE,
        ComposerJsonSection::LICENSE,
        ComposerJsonSection::AUTHORS,
        ComposerJsonSection::BIN,
        ComposerJsonSection::REQUIRE,
        ComposerJsonSection::REQUIRE_DEV,
        ComposerJsonSection::AUTOLOAD,
        ComposerJsonSection::AUTOLOAD_DEV,
        ComposerJsonSection::REPOSITORIES,
        ComposerJsonSection::PROVIDES,
        ComposerJsonSection::CONFLICT,
        ComposerJsonSection::REPLACE,
        ComposerJsonSection::SCRIPTS,
        ComposerJsonSection::SCRIPTS_DESCRIPTIONS,
        ComposerJsonSection::SUGGESTS,
        ComposerJsonSection::CONFIG,
        ComposerJsonSection::MINIMUM_STABILITY,
        ComposerJsonSection::PREFER_STABLE,
        ComposerJsonSection::EXTRA,
    ]);
};
