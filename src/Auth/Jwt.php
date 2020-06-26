<?php

namespace AtlassianConnectLaravel\Auth;

use Firebase\JWT\JWT as FirebaseJwt;
use UnexpectedValueException;

class Jwt
{
    /**
     * Decode JWT token.
     */
    public static function decodeWithoutVerifying(string $token): object
    {
        [$header, $body, $signature] = explode('.', $token);

        return (object) [
            'header' => FirebaseJwt::jsonDecode(FirebaseJwt::urlsafeB64Decode($header)),
            'body' => FirebaseJwt::jsonDecode(FirebaseJwt::urlsafeB64Decode($body)),
            'signature' => $signature,
        ];
    }

    /**
     * Verify token (signature and query string hash).
     */
    public static function verify(
        string $token,
        string $algorithm,
        string $secret,
        string $url,
        string $method
    ): bool {
        try {
            $data = FirebaseJwt::decode($token, $secret, [$algorithm]);
        } catch (UnexpectedValueException $e) {
            return false;
        }

        if ($data->qsh !== (new Qsh($url, $method))->create()) {
            return false;
        }

        return true;
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
            'qsh' => (new Qsh($url, $method))->create(),
        ];

        return FirebaseJwt::encode($payload, $secret);
    }
}
