<?php

namespace AtlassianConnectLaravel\Api;

use AtlassianConnectLaravel\Auth\Jwt;
use AtlassianConnectLaravel\Models\Tenant;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\LazyCollection;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

class ApiClient
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

    public function post(string $uri, array $query = [], array $body = [])
    {
        return $this->request('post', $uri, ['query' => $query, 'json' => $body]);
    }

    public function put(string $uri, array $query = [], array $body = [])
    {
        return $this->request('put', $uri, ['query' => $query, 'json' => $body]);
    }

    public function request(string $method, string $uri = '', array $options = [])
    {
        $response = $this->client->request($method, $uri, $options);

        $contents = $response->getBody()->getContents();
        $decoded = json_decode($contents, true);

        return $decoded ? $decoded : $contents;
    }

    public function paginated(
        string $method,
        string $uri = '',
        array $options = [],
        string $valuesKey = 'values',
        int $maxResults = 50
    ): LazyCollection {
        $method = strtolower($method);

        if ($method !== 'get' || $method !== 'post') {
            new InvalidArgumentException('Method parameter must be get or post.');
        }

        return LazyCollection::make(function () use ($method, $uri, $options, $valuesKey, $maxResults) {
            $startAt = 0;

            do {
                $optionKey = $method === 'get' ? 'query' : 'json';
                $options[$optionKey] = array_merge($options[$optionKey] ?? [], [
                    'startAt' => $startAt,
                    'maxResults' => $maxResults,
                ]);
                $data = $this->request($method, $uri, $options);

                if (!isset($data['total'])) {
                    new InvalidArgumentException('Response data must have property total.');
                }

                if (!isset($data[$valuesKey])) {
                    new InvalidArgumentException("Response data must have property {$valuesKey}.");
                }

                $total = $data['total'];
                $isNotLast = ($total - $startAt) > $maxResults;
                $startAt += $maxResults;

                foreach ($data[$valuesKey] as $value) {
                    yield $value;
                }
            } while ($isNotLast);
        });
    }

    protected static function getAuthMiddleware(Tenant $tenant): callable
    {
        return Middleware::mapRequest(
            function (RequestInterface $request) use ($tenant) {
                $token = Jwt::create(
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
