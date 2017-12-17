<?php declare(strict_types=1);

require_once __DIR__ . '/changelog-linker-bootstrap.php';

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\ChangelogLinker\ChangelogApplication;

$input = new ArgvInput();
if ($input->getFirstArgument() === null) {
    die('Use path to CHANGELOG.md file as first argument' . PHP_EOL);
}

$filePath = $input->getFirstArgument();
if (! file_exists($filePath)) {
    die(sprintf('Changelog file "%s" was not found' . PHP_EOL, $filePath));
}

// path as arg...

$changelogApplication = new ChangelogApplication;
$changelogApplication->loadFile($filePath);

$changelogApplication->completeBracketsAroundReferences();
$changelogApplication->completeLinksToIds();
$changelogApplication->completeDiffLinksToVersions();

$changelogApplication->appendLinks();

// 3. links to commits
