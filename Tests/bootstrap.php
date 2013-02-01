<?php

define('DECODA', __DIR__.'/../../../../../mjohnson/decoda/src/Decoda');

$file = __DIR__.'/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies to run test suite.');
}

$autoload = require_once $file;