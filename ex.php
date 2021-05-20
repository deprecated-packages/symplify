<?php

$dirs = scandir('packages');
foreach ($dirs as $dir) {
    if ($dir !== '.' && $dir !== '..') {
        if ($dir !== 'amnesia') {
            shell_exec("cp packages/amnesia/.github/workflows/auto_closer.yaml packages/$dir/.github/workflows/auto_closer.yaml");
            shell_exec("rm -rf packages/$dir/.github/workflows/repo-lockdown.yaml");
        }
    }
}