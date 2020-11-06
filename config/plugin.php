<?php

return [
    'apiVersion' => 2,
    'events' => [],
    'overrides' => [
        'tenant' => \AtlassianConnectLaravel\Models\Tenant::class,
    ],
    'descriptor' => [
        'name' => config('app.name'),
        'key' => 'com.atlassian.new-plugin',
        'baseUrl' => request()->getSchemeAndHttpHost(),
        'version' => '1.0.0',
        'authentication' => [
            'type' => 'jwt',
        ],
        'lifecycle' => [
            'installed' => '/installed',
            'uninstalled' => '/uninstalled',
            'enabled' => '/enabled',
            'disabled' => '/disabled',
        ],
        'scopes' => [
            'ADMIN',
            'ACT_AS_USER',
        ],
    ],
];
