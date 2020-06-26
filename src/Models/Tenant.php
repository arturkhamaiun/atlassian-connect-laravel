<?php

namespace AtlassianConnectLaravel\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model implements AuthenticatableContract
{
    use Authenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'client_key',
        'oauth_client_id',
        'shared_secret',
        'base_url',
        'product_type',
        'description',
        'event_type',
    ];

    public function isInstalled(): bool
    {
        return !$this->isUninstalled();
    }

    public function isEnabled(): bool
    {
        return $this->event_type === 'enabled';
    }

    public function isDisabled(): bool
    {
        return $this->event_type === 'disabled';
    }

    public function isUninstalled(): bool
    {
        return $this->event_type === 'uninstalled';
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $property => $value) {
            unset($attributes[$property]);
            $attributes[Str::snake($property)] = $value;
        }

        return parent::fill($attributes);
    }
}
