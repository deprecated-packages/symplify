<?php

if (file_exists($autoload = __DIR__.'/../../../autoload.php')) {
    return require_once $autoload;
} else {
    return require_once __DIR__.'/../vendor/autoload.php';
}
