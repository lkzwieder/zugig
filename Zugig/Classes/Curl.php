<?php

class Curl
{
    public static function request(
        string $url,
        array $params = [],
        string $method = 'GET',
        ?array $headers = null
    ): array {
        $ch = self::createHandle($url, $params, $method, $headers);

        try {
            $content = curl_exec($ch);
            return self::processResponse($ch, $content);
        } catch (Throwable $e) {
            return self::errorResponse($e);
        } finally {
            curl_close($ch);
        }
    }

    public static function parallel(array $requests): array
    {
        $handles = [];
        $map = [];

        foreach ($requests as $i => $req) {
            $url = $req['url'] ?? '';
            $method = $req['method'] ?? 'GET';
            $params = $req['params'] ?? [];
            $headers = $req['headers'] ?? null;

            $handles[$i] = self::createHandle($url, $params, $method, $headers);
            $map[$i] = $handles[$i];
        }

        $mh = curl_multi_init();
        $responses = [];

        try {
            foreach ($handles as $ch) {
                curl_multi_add_handle($mh, $ch);
            }

            $running = null;
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh);
            } while ($running > 0);

            foreach ($map as $i => $ch) {
                $content = curl_multi_getcontent($ch);
                $responses[$i] = self::processResponse($ch, $content);
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
            }

            return $responses;
        } catch (Throwable $e) {
            return array_fill_keys(array_keys($requests), self::errorResponse($e));
        } finally {
            curl_multi_close($mh);
        }
    }

    private static function createHandle(
        string $url,
        array $params,
        string $method,
        ?array $headers
    ): \CurlHandle {
        if (!empty($params) && in_array($method, ['GET', 'DELETE'], true)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        if ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        if ($headers !== null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        return $ch;
    }

    private static function processResponse(\CurlHandle $ch, ?string $content): array
    {
        $error = curl_errno($ch);
        if ($error > 0) {
            return [
                'code' => 0,
                'body' => null,
                'error' => curl_error($ch),
                'headers' => [],
            ];
        }

        return [
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'body' => $content,
            'error' => null,
            'headers' => [],
        ];
    }

    private static function errorResponse(Throwable $e): array
    {
        return [
            'code' => 0,
            'body' => null,
            'error' => $e->getMessage(),
            'headers' => [],
        ];
    }
}

// Uso:
// $result = Curl::request('https://api.example.com/user', ['id' => 1]);
// $results = Curl::parallel([
//     ['url' => 'https://api.example.com/users'],
//     ['url' => 'https://api.example.com/posts'],
//     ['method' => 'POST', 'url' => 'https://api.example.com/create', 'params' => ['name' => 'test']],
// ]);
