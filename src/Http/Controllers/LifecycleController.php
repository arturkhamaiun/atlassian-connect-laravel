<?php

namespace AtlassianConnectLaravel\Http\Controllers;

use AtlassianConnectLaravel\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LifecycleController
{
    public function installed(Request $request)
    {
        $tenant = Tenant::create($request->all());

        Auth::setUser($tenant);

        return response(null, 204);
    }

    public function enabled(Request $request)
    {
        $request->user()->update($request->all());

        return response(null, 204);
    }

    public function disabled(Request $request)
    {
        $request->user()->update($request->all());

        return response(null, 204);
    }

    public function uninstalled(Request $request)
    {
        $request->user()->delete();

        return response(null, 204);
    }
}
