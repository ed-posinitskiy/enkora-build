<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new \Dotenv\Dotenv(__DIR__ . '/../'))->load();

$kernel = new \App\Kernel(__DIR__ . '/../config/app.php');

return $kernel->bootstrap();
