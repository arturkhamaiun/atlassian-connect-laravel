<?php

namespace AtlassianConnectLaravel\Http\Controllers;

use AtlassianConnectLaravel\Events\Disabled;
use AtlassianConnectLaravel\Events\Enabled;
use AtlassianConnectLaravel\Events\Installed;
use AtlassianConnectLaravel\Events\Uninstalled;
use AtlassianConnectLaravel\Http\Requests\InstalledRequest;
use AtlassianConnectLaravel\Http\Requests\LifecycleRequest;
use AtlassianConnectLaravel\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class LifecycleController
{
    public function installed(InstalledRequest $request)
    {
        $tenant = Tenant::updateOrCreate([
            'key' => $request->key,
            'shared_secret' => $request->sharedSecret,
        ], $request->all());

        Auth::setUser($tenant);
        Installed::dispatch();

        return response(null, 204);
    }

    public function enabled(LifecycleRequest $request)
    {
        $request->user()->update($request->all());

        Enabled::dispatch();

        return response(null, 204);
    }

    public function disabled(LifecycleRequest $request)
    {
        $request->user()->update($request->all());

        Disabled::dispatch();

        return response(null, 204);
    }

    public function uninstalled(LifecycleRequest $request)
    {
        $request->user()->delete();

        Uninstalled::dispatch();

        return response(null, 204);
    }
}
