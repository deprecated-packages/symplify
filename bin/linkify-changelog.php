<?php declare(strict_types=1);

use Symplify\Changelog\ChangelogApplication;

require_once __DIR__ . '/../vendor/autoload.php';

$changelogApplication = new ChangelogApplication;
$changelogApplication->loadFile(__DIR__ . '/../CHANGELOG.md');

$changelogApplication->completeBracketsAroundReferences();
$changelogApplication->completeLinksToIds();
$changelogApplication->completeDiffLinksToVersions();

$changelogApplication->appendLinks();

// 3. links to commits
