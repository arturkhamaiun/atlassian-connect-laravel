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
        PaginationInfo $paginationInfo
    ): LazyCollection {
        $method = strtolower($method);

        if ($method !== 'get' || $method !== 'post') {
            new InvalidArgumentException('Method parameter must be get or post.');
        }

        return LazyCollection::make(function () use ($method, $uri, $options, $paginationInfo) {
            $offset = $paginationInfo->getOffset();
            $limit = $paginationInfo->getLimit();

            do {
                $optionKey = $method === 'get' ? 'query' : 'json';
                $options[$optionKey] = array_merge($options[$optionKey] ?? [], [
                    $paginationInfo->getOffsetKey() => $offset,
                    $paginationInfo->getLimitKey() => $limit,
                ]);
                $data = $this->request($method, $uri, $options);

                if (!isset($data[$paginationInfo->getTotalKey()])) {
                    new InvalidArgumentException('Response data must have property total.');
                }

                if (!isset($data[$paginationInfo->getResultsKey()])) {
                    new InvalidArgumentException("Response data must have property {$paginationInfo->getResultsKey()}.");
                }

                $total = $data[$paginationInfo->getTotalKey()];
                $isNotLast = ($total - $offset) > $limit;
                $offset += $limit;

                foreach ($data[$paginationInfo->getResultsKey()] as $value) {
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
