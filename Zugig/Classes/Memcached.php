<?php

class Memcached
{
    private \Memcached $client;

    public function __construct(array $servers = [], int $timeout = 1)
    {
        $this->client = new \Memcached();

        if (empty($servers)) {
            $defaultServer = [
                'host' => $_ENV['MEMCACHED_HOST'] ?? '127.0.0.1',
                'port' => (int) ($_ENV['MEMCACHED_PORT'] ?? 11211),
            ];
            $this->client->addServer($defaultServer['host'], $defaultServer['port'], $weight = 1);
        } else {
            foreach ($servers as $server) {
                $this->client->addServer(
                    $server['host'] ?? '127.0.0.1',
                    $server['port'] ?? 11211,
                    $server['weight'] ?? 1
                );
            }
        }

        $this->client->setOption(\Memcached::OPT_TIMEOUT, $timeout);
        $this->client->setOption(\Memcached::OPT_RETRY_TIMEOUT, $timeout);
    }

    public function get(string $key): mixed
    {
        return $this->client->get($key);
    }

    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        return $this->client->set($key, $value, $ttl);
    }

    public function delete(string $key): bool
    {
        return $this->client->delete($key) !== false;
    }

    public function deleteMultiple(array $keys): bool
    {
        return $this->client->deleteMulti($keys) !== false;
    }

    public function flush(): bool
    {
        return $this->client->flush();
    }

    public function getMultiple(array $keys): array
    {
        return $this->client->getMulti($keys);
    }

    public function setMultiple(array $items, int $ttl = 0): bool
    {
        return $this->client->setMulti($items, $ttl);
    }
}

// Uso:
// $cache = new Memcached([['host' => 'memcache.local', 'port' => 11211]]);
// $cache->set('users', $data, 3600);
// $value = $cache->get('users');
// $cache->delete('users');
