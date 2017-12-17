<?php declare(strict_types=1);

require_once __DIR__ . '/changelog-linker-bootstrap.php';

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\Worker\CompleteBracketsAroundReferencesWorker;
use Symplify\ChangelogLinker\Worker\CompleteDiffLinksToVersionsWorker;

$input = new ArgvInput();
if ($input->getFirstArgument() === null) {
    die('Use path to CHANGELOG.md file as first argument' . PHP_EOL);
}

$filePath = $input->getFirstArgument();
if (! file_exists($filePath)) {
    die(sprintf('Changelog file "%s" was not found' . PHP_EOL, $filePath));
}

$changelogApplication = new ChangelogApplication('https://github.com/Symplify/Symplify');
$changelogApplication->addWorker(new CompleteBracketsAroundReferencesWorker());
$changelogApplication->addWorker(new CompleteDiffLinksToVersionsWorker());

$changelogApplication->completeLinksToIds();

$changelogApplication->processFile($filePath);
$changelogApplication->saveContent();

// 3. links to commits
