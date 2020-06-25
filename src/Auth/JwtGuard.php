<?php

namespace AtlassianConnectLaravel\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Arr;

class JwtGuard implements Guard
{
    use GuardHelpers;

    public function __construct(UserProvider $provider)
    {
        $this->setProvider($provider);
    }

    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = last(explode(' ', request('jwt', request()->header('Authorization'))));

        if (!$token) {
            return null;
        }

        return $this->getUserByToken($token);
    }

    public function validate(array $credentials = [])
    {
        $token = $credentials['token'] ?? null;

        if (!$token) {
            return null;
        }

        return (bool) $this->getUserByToken($token);
    }

    public function getUserByToken(string $token): ?Authenticatable
    {
        //TODO: Decoding and verifying a JWT token
        return $this->provider->retrieveByCredentials(
            ['client_key' => Arr::get(JwtHelper::decode($token), 'body.iss')]
        );
    }
}
