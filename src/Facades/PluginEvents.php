<?php

namespace AtlassianConnectLaravel\Facades;

use AtlassianConnectLaravel\PluginEvents as PluginEventsClass;
use Illuminate\Support\Facades\Facade;

class PluginEvents extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PluginEventsClass::class;
    }
}
