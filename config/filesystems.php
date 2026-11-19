<?php

return [
    'default' => env('FILESYSTEM_DISK', 'private'),
    'disks' => [
        'private' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'throw' => true,
        ],
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'throw' => true,
        ],
    ],
];
