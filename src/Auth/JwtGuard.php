<?php

namespace AtlassianConnectLaravel\Auth;

use AtlassianConnectLaravel\Models\Tenant;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JwtGuard implements Guard
{
    use GuardHelpers;

    protected Request $request;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->setProvider($provider);
        $this->request = $request;
    }

    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = $this->extractJwtFromRequest();

        if (!$token) {
            return null;
        }

        return $this->getTenantByToken($token);
    }

    public function validate(array $credentials = [])
    {
        $token = $credentials['token'] ?? null;

        if (!$token) {
            return null;
        }

        return (bool) $this->getTenantByToken($token);
    }

    protected function extractJwtFromRequest()
    {
        $token = $this->request->header('Authorization', $this->request->get('jwt'));

        return last(explode(' ', $token));
    }

    protected function getTenantByToken(string $token): ?Tenant
    {
        $data = Jwt::decodeWithoutVerifying($token);

        $tenant = $this->provider->retrieveByCredentials(
            ['client_key' => $data->body->iss]
        );

        if ($tenant === null) {
            return null;
        }

        $verificationResult = Jwt::verify(
            $token,
            $data->header->alg,
            $tenant->shared_secret,
            $this->request->getUri(),
            $this->request->getMethod(),
        );

        return $verificationResult ? $tenant : null;
    }
}
