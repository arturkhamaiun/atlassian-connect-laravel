<?php

namespace AtlassianConnectLaravel;

use AtlassianConnectLaravel\Facades\PluginEvents;
use Illuminate\Support\Arr;

class AppDescriptor
{
    protected array $content = [];

    public function get(): array
    {
        $this->content = config('plugin.descriptor');

        $this->buildWebhookEvents();

        return $this->content;
    }

    public function buildWebhookEvents(): void
    {
        foreach (PluginEvents::getRegisteredWebhookEvents() as $event) {
            $webhooks[] = [
                'event' => $event,
                'url' => route('webhook', ['event' => $event], false),
            ];
        }

        !empty($webhooks) && Arr::set($this->content, 'modules.webhooks', $webhooks);
    }
}
