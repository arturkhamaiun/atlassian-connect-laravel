<?php

namespace AtlassianConnectLaravel;

use Illuminate\Support\Arr;

class AppDescriptor
{
    public function get()
    {
        $descriptor = config('descriptor');

        foreach (PluginEvents::getRegisteredWebhookEvents() as $event) {
            $webhooks[] = [
                'event' => $event,
                'url' => route('webhook', ['event' => $event], false)
            ];

        }

        !empty($webhooks) && Arr::set($descriptor, 'modules.webhooks', $webhooks);

        return $descriptor;
    }
}
