<?php

namespace AtlassianConnectLaravel;

use Illuminate\Support\Arr;

class AppDescriptor
{
    protected array $content = [];

    public function get(): array
    {
        $this->content = config('descriptor');

        $this->buildWebhookEvents();

        return $this->content;
    }

    public function buildWebhookEvents(): void
    {
        foreach (PluginEvents::getRegisteredWebhookEvents() as $event) {
            $webhooks[] = [
                'event' => $event,
                'url' => route('webhook', ['event' => $event], false)
            ];

        }

        !empty($webhooks) && Arr::set($content, 'modules.webhooks', $webhooks);
    }
}
