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

    public function fill(array $attributes)
    {
        foreach ($attributes as $property => $value) {
            unset($attributes[$property]);
            $attributes[Str::snake($property)] = $value;
        }

        return parent::fill($attributes);
    }
}
