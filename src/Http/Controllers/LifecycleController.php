<?php

namespace AtlassianConnectLaravel\Http\Controllers;

use AtlassianConnectLaravel\Events\Disabled;
use AtlassianConnectLaravel\Events\Enabled;
use AtlassianConnectLaravel\Events\Installed;
use AtlassianConnectLaravel\Events\Uninstalled;
use AtlassianConnectLaravel\Http\Requests\InstalledRequest;
use AtlassianConnectLaravel\Http\Requests\LifecycleRequest;
use Illuminate\Support\Facades\Auth;

class LifecycleController
{
    public function installed(InstalledRequest $request)
    {
        $tenant = config('plugin.overrides.tenant')::updateOrCreate([
            'key' => $request->key,
            'shared_secret' => $request->sharedSecret,
        ], $request->all());

        Auth::setUser($tenant);
        Installed::dispatch($tenant);

        return response(null, 204);
    }

    public function enabled(LifecycleRequest $request)
    {
        $request->user()->update($request->all());

        Enabled::dispatch($request->user());

        return response(null, 204);
    }

    public function disabled(LifecycleRequest $request)
    {
        $request->user()->update($request->all());

        Disabled::dispatch($request->user());

        return response(null, 204);
    }

    public function uninstalled(LifecycleRequest $request)
    {
        Uninstalled::dispatch($request->user());

        $request->user()->delete();

        return response(null, 204);
    }
}
