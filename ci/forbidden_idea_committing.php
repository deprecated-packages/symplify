<?

if (file_exists(getcwd() . '/.idea')) {
    die('CI FAILED: .idea folder should not be part of the codebase. Please remove the .idea folder');
}
