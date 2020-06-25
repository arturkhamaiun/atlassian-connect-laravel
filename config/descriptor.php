<?php

return [
    'name' => config('app.name'),
    'key' => 'com.atlassian.new-plugin',
    'baseUrl' => config('app.url'),
    'version' => '1.0.0',
    'authentication' => [
        'type' => 'jwt',
    ],
    'lifecycle' => [
        'installed' => route('installed', [], false),
        'uninstalled' => route('uninstalled', [], false),
        'enabled' => route('enabled', [], false),
        'disabled' => route('disabled', [], false),
    ],
    'scopes' => [
        'ADMIN',
        'ACT_AS_USER',
    ],
];
