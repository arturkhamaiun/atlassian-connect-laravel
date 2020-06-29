<?php

namespace AtlassianConnectLaravel\Http\Controllers;

use AtlassianConnectLaravel\AppDescriptor;
use AtlassianConnectLaravel\Facades\PluginEvents;
use AtlassianConnectLaravel\Http\Requests\InstalledRequest;
use AtlassianConnectLaravel\Http\Requests\LifecycleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PluginController
{
    public function connect(AppDescriptor $appDescriptor)
    {
        return response()->json($appDescriptor->get());
    }

    public function installed(InstalledRequest $request)
    {
        $tenantClass = config('plugin.overrides.tenant');
        $tenant = new $tenantClass($request->all());
        $tenant->oauth_client_id = $request->oauthClientId;
        $tenant->shared_secret = $request->sharedSecret;
        $tenant->save();

        Auth::setUser($tenant);
        PluginEvents::dispatch('installed', $request);

        return response(null, 200);
    }

    public function enabled(LifecycleRequest $request)
    {
        $request->user()->update($request->all());

        PluginEvents::dispatch('enabled', $request);

        return response(null, 200);
    }

    public function disabled(LifecycleRequest $request)
    {
        $request->user()->update($request->all());

        PluginEvents::dispatch('disabled', $request);

        return response(null, 200);
    }

    public function uninstalled(LifecycleRequest $request)
    {
        PluginEvents::dispatch('uninstalled', $request);

        $request->user()->delete();

        return response(null, 200);
    }

    public function webhook(string $event, Request $request)
    {
        PluginEvents::dispatch($event, $request);

        return response(null, 200);
    }
}
