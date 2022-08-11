<?php
return [
    'enabled' => env('AUDITING_ENABLED', true),

    'events' => [
        'created',
        'updating',
        'deleted',
    ],
];
