<?php

namespace AtlassianConnectLaravel\Events;

use AtlassianConnectLaravel\Models\Tenant;
use Illuminate\Foundation\Events\Dispatchable;

class LifecycleEvent
{
    use Dispatchable;

    public Tenant $tenant;

    public function __construct(Tenant $tenant)
    {
        return $this->tenant = $tenant;
    }
}
