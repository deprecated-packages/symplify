<?php declare(strict_types=1);

require_once __DIR__ . '/changelog-linker-bootstrap.php';

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\Worker\BracketsAroundReferencesWorker;
use Symplify\ChangelogLinker\Worker\DiffLinksToVersionsWorker;
use Symplify\ChangelogLinker\Worker\LinksToReferencesWorker;
use Symplify\ChangelogLinker\Worker\ShortenReferencesWorker;
use Symplify\ChangelogLinker\Worker\UserReferencesWorker;

$input = new ArgvInput();
if ($input->getFirstArgument() === null) {
    die('Use path to CHANGELOG.md file as first argument' . PHP_EOL);
}

$filePath = $input->getFirstArgument();
if (! file_exists($filePath)) {
    die(sprintf('Changelog file "%s" was not found' . PHP_EOL, $filePath));
}

// --repository https://github.com/rectorphp/rector
if ($input->getParameterOption('--repository')) {
    $repository = $input->getParameterOption('--repository');
} else {
    $repository = 'https://github.com/Symplify/Symplify';
}

$changelogApplication = new ChangelogApplication($repository);

// order matters, as later depend on former
$changelogApplication->addWorker(new BracketsAroundReferencesWorker());
$changelogApplication->addWorker(new DiffLinksToVersionsWorker());
$changelogApplication->addWorker(new LinksToReferencesWorker());
$changelogApplication->addWorker(new ShortenReferencesWorker());
$changelogApplication->addWorker(new UserReferencesWorker());

$changelogApplication->processFileAndSave($filePath);
