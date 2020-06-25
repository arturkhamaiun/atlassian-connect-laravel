<?php

namespace AtlassianConnectLaravel\Auth;

use Firebase\JWT\JWT;

class JwtHelper
{
    /**
     * Decode JWT token.
     */
    public static function decode(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        return [
            'header' => json_decode(base64_decode($parts[0]), true),
            'body' => json_decode(base64_decode($parts[1]), true),
            'signature' => $parts[2],
        ];
    }

    /**
     * Create JWT token used by Atlassian REST API request.
     */
    public static function create(string $url, string $method, string $issuer, string $secret): string
    {
        $payload = [
            'iss' => $issuer,
            'iat' => time(),
            'exp' => time() + 86400,
            'qsh' => (new QSH($url, $method))->create(),
        ];

        return JWT::encode($payload, $secret);
    }
}
