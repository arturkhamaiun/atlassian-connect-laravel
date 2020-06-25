<?php

namespace AtlassianConnectLaravel;

use AtlassianConnectLaravel\Auth\JwtHelper;
use AtlassianConnectLaravel\Models\Tenant;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class HttpClient
{
    public Client $client;

    protected function __construct(Client $client)
    {
        $this->client = $client;
    }

    public static function create(Tenant $tenant, int $apiVersion, array $config = [])
    {
        $stack = HandlerStack::create();
        $stack->push(static::getAuthMiddleware($tenant));

        // $messageFormats = [
        //     'REQUEST: {method} - {uri} - HTTP/{version} - {req_headers} - {req_body}',
        //     'RESPONSE: {code} - {res_body}',
        // ];

        // collect($messageFormats)->each(function ($messageFormat) use ($stack) {
        //     $stack->push(
        //         Middleware::log(
        //             with(new \Monolog\Logger('laravel'))->pushHandler(
        //                 new \Monolog\Handler\StreamHandler(storage_path('logs/laravel.log'))
        //             ),
        //             new \GuzzleHttp\MessageFormatter($messageFormat)
        //         )
        //     );
        // });

        $config['handler'] = $stack;
        $config['base_uri'] = "{$tenant->base_url}/rest/api/{$apiVersion}/";

        return new self(new Client($config));
    }

    public function get(string $uri, array $query = [])
    {
        return $this->request('get', $uri, ['query' => $query]);
    }

    public function post(string $uri, array $body = [])
    {
        return $this->request('post', $uri, ['json' => $body]);
    }

    public function request(string $method, string $uri = '', array $options = [])
    {
        $response = $this->client->request($method, $uri, $options);

        $contents = $response->getBody()->getContents();
        $decoded = json_decode($contents, true);

        return $decoded ? $decoded : $contents;
    }

    protected static function getAuthMiddleware(Tenant $tenant): callable
    {
        return Middleware::mapRequest(
            function (RequestInterface $request) use ($tenant) {
                $token = JwtHelper::create(
                    $request->getUri(),
                    $request->getMethod(),
                    $tenant->key,
                    $tenant->shared_secret
                );

                return $request->withAddedHeader('Authorization', "JWT {$token}");
            }
        );
    }
}
