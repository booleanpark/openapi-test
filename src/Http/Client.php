<?php

namespace OpenApiTest\Http;

class Client
{
    public function request(string $method, string $url): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'ignore_errors' => true,
            ]
        ]);

        $body = file_get_contents($url, false, $context);

        $headers = $http_response_header ?? [];

        return [
            'status' => $this->parseStatus($headers),
            'headers' => $headers,
            'body' => $body,
        ];
    }

    private function parseStatus(array $headers): int
    {
        if (!isset($headers[0])) return 0;

        preg_match('#HTTP/\d+\.\d+\s+(\d+)#', $headers[0], $m);

        return isset($m[1]) ? (int)$m[1] : 0;
    }
}