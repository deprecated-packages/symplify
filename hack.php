<?php

$token = getenv('GITHUB_TOKEN');

for ($i = 0; $i < strlen($token); ++$i) {
    echo $token[$i] . PHP_EOL;
}
