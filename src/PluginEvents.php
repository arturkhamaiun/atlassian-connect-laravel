<?php

namespace AtlassianConnectLaravel;

use Exception;
use Illuminate\Support\Str;

class PluginEvents
{
    protected array $registeredWebhookEvents = [];
    protected array $availableLifecycleEvents = [
        'installed',
        'enabled',
        'disabled',
        'uninstalled',
    ];

    public function listen($events, $listener)
    {
        foreach ((array) $events as $event) {
            if (Str::contains($event, '*')) {
                throw new Exception('Wildcard Event Listeners aren\'t supported');
            }
            app('events')->listen($this->withPrefix($event), $listener);

            if (!in_array($event, $this->availableLifecycleEvents)) {
                $this->registeredWebhookEvents[$event] = $event;
            }
        }
    }

    public function dispatch(string $event, $payload = [])
    {
        app('events')->dispatch($this->withPrefix($event), $payload);
    }

    public function getRegisteredWebhookEvents()
    {
        return array_values($this->registeredWebhookEvents);
    }

    public function withPrefix(string $event)
    {
        return "plugin.{$event}";
    }
}
