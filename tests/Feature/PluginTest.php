<?php

namespace AtlassianConnectLaravel\Tests\Feature;

use AtlassianConnectLaravel\Facades\PluginEvents;
use AtlassianConnectLaravel\Models\Tenant;
use AtlassianConnectLaravel\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

class PluginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withHeader('Accept', 'application/json');
        Event::fake();
    }

    public function testConnect()
    {
        $response = $this->get('/connect');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'name',
            'key',
            'baseUrl',
            'version',
            'authentication',
            'lifecycle',
            'scopes',
        ]);
    }

    public function testInstalled()
    {
        $response = $this->post('/installed', [
            'key' => 'dummy',
            'clientKey' => 'dummy',
            'oauthClientId' => 'dummy',
            'sharedSecret' => 'dummy',
            'baseUrl' => 'dummy',
            'productType' => 'dummy',
            'description' => 'dummy',
            'eventType' => 'installed',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tenants', [
            'key' => 'dummy',
            'client_key' => 'dummy',
            'oauth_client_id' => 'dummy',
            'shared_secret' => 'dummy',
            'base_url' => 'dummy',
            'product_type' => 'dummy',
            'description' => 'dummy',
            'event_type' => 'installed',
        ]);

        Event::assertDispatched(PluginEvents::withPrefix('installed'));
    }

    public function testEnabled()
    {
        $tenant = factory(Tenant::class)->create();

        $response = $this->be($tenant, 'plugin')->post('/enabled', [
            'key' => 'enabled_dummy',
            'clientKey' => 'enabled_dummy',
            'baseUrl' => 'enabled_dummy',
            'productType' => 'enabled_dummy',
            'description' => 'enabled_dummy',
            'eventType' => 'enabled',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'key' => 'enabled_dummy',
            'client_key' => 'enabled_dummy',
            'base_url' => 'enabled_dummy',
            'product_type' => 'enabled_dummy',
            'description' => 'enabled_dummy',
            'event_type' => 'enabled',
        ]);

        Event::assertDispatched(PluginEvents::withPrefix('enabled'));
    }

    public function testDisabled()
    {
        $tenant = factory(Tenant::class)->create();

        $response = $this->be($tenant, 'plugin')->post('/disabled', [
            'key' => 'disabled_dummy',
            'clientKey' => 'disabled_dummy',
            'baseUrl' => 'disabled_dummy',
            'productType' => 'disabled_dummy',
            'description' => 'disabled_dummy',
            'eventType' => 'disabled',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'key' => 'disabled_dummy',
            'client_key' => 'disabled_dummy',
            'base_url' => 'disabled_dummy',
            'product_type' => 'disabled_dummy',
            'description' => 'disabled_dummy',
            'event_type' => 'disabled',
        ]);

        Event::assertDispatched(PluginEvents::withPrefix('disabled'));
    }

    public function testUninstalled()
    {
        $tenant = factory(Tenant::class)->create();

        $response = $this->be($tenant, 'plugin')->post('/uninstalled', [
            'key' => 'uninstalled_dummy',
            'clientKey' => 'uninstalled_dummy',
            'baseUrl' => 'uninstalled_dummy',
            'productType' => 'uninstalled_dummy',
            'description' => 'uninstalled_dummy',
            'eventType' => 'uninstalled',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);

        Event::assertDispatched(PluginEvents::withPrefix('uninstalled'));
    }

    public function testWebhook()
    {
        $tenant = factory(Tenant::class)->create();

        $response = $this->be($tenant, 'plugin')->post('/webhook/some-random-event');

        $response->assertStatus(200);

        Event::assertDispatched(PluginEvents::withPrefix('some-random-event'));
    }
}
