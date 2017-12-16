<?php declare(strict_types=1);

use Symplify\Changelog\ChangelogApplication;

require_once __DIR__ . '/../vendor/autoload.php';

$changelogApplication = new ChangelogApplication;
$changelogApplication->loadFile(__DIR__ . '/../CHANGELOG.md');
$changelogApplication->completeLinksToIds();

// 1. autocomplete diffs
// 3. links to commits
