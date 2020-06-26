<?php

namespace AtlassianConnectLaravel\Http\Controllers;

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

        return response(null, 204);
    }

    public function enabled(LifecycleRequest $request)
    {
        $request->user()->update($request->all());

        return response(null, 204);
    }

    public function disabled(LifecycleRequest $request)
    {
        $request->user()->update($request->all());

        return response(null, 204);
    }

    public function uninstalled(LifecycleRequest $request)
    {
        $request->user()->delete();

        return response(null, 204);
    }
}
