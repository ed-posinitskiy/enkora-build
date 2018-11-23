<?php

use App\Command\BuildAngularCommand;
use App\Provider\AppServiceProvider;

return [
    'providers' => [
        AppServiceProvider::class,
    ],
    'commands'  => [
        BuildAngularCommand::class,
    ],
    'aws'       => [
        'key'    => env('AWS_KEY'),
        'secret' => env('AWS_SECRET'),
        'region' => env('AWS_REGION'),
        'bucket' => env('AWS_BUCKET'),
    ],
];