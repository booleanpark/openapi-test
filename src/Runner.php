<?php
namespace OpenApiTest;

use GuzzleHttp\Client;
use Symfony\Component\Yaml\Yaml;

class Runner
{
    public function run(string $specFile, string $baseUrl): array
    {
        $spec = Yaml::parseFile($specFile);
        $client = new Client();
        $results = [];

        foreach (($spec['paths'] ?? []) as $path => $methods) {
            foreach ($methods as $method => $config) {
                if (strtolower($method) !== 'get') {
                    continue;
                }

                $url = $baseUrl . preg_replace('/\{[^}]+\}/', '1', $path);

                try {
                    $response = $client->request(strtoupper($method), $url);

                    $body = (string) $response->getBody();
                    json_decode($body);

                    $errors = [];

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $errors[] = 'Response is not valid JSON';
                    }

                    $results[] = new Result(
                        strtoupper($method) . ' ' . $path,
                        empty($errors),
                        $errors
                    );
                } catch (\Throwable $e) {
                    $results[] = new Result(
                        strtoupper($method) . ' ' . $path,
                        false,
                        [$e->getMessage()]
                    );
                }
            }
        }

        return $results;
    }
}
