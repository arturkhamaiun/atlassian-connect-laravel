<?php

namespace AtlassianConnectLaravel;

use Exception;
use Illuminate\Support\Str;

class PluginEvents
{
    protected static array $registeredWebhookEvents = [];
    protected static array $availableLifecycleEvents = [
        'installed',
        'enabled',
        'disabled',
        'uninstalled'
    ];

    public static function listen($events, $listener)
    {
        foreach ((array) $events as $event) {
            if (Str::contains($event, '*')) {
                throw new Exception('Wildcard Event Listeners aren\'t supported');
            } else {
                app('events')->listen(self::withPrefix($event), $listener);

                if (!in_array($event, self::$availableLifecycleEvents)) {
                    self::$registeredWebhookEvents[$event] = $event;
                }
            }
        }
    }

    public static function dispatch(string $event, $payload = [])
    {
        app('events')->dispatch(self::withPrefix($event), $payload);
    }

    public static function getRegisteredWebhookEvents()
    {
        return array_values(self::$registeredWebhookEvents);
    }

    protected static function withPrefix(string $event)
    {
        return "plugin.{$event}";
    }
}
