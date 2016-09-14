<?php

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../conf/slim.php';
$app = new \Slim\App($settings);

$app->run();
