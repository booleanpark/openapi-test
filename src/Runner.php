<?php

namespace OpenApiTest;

use OpenApiTest\Http\Client;
use OpenApiTest\Spec\SpecLoader;
use OpenApiTest\Spec\PathResolver;
use OpenApiTest\Validation\ResponseValidator;

class Runner
{
    public function run(string $specFile, string $baseUrl): array
    {
        $spec = (new SpecLoader())->load($specFile);

        $client = new Client();
        $validator = new ResponseValidator($spec);

        $results = [];

        foreach ($spec['paths'] ?? [] as $path => $methods) {
            foreach ($methods as $method => $operation) {

                if (strtolower($method) !== 'get') {
                    continue;
                }

                $url = $baseUrl . PathResolver::resolve($path);

                try {
                    $response = $client->request('GET', $url);

                    $result = $validator->validate($method, $path, $operation, $response);

                    $results[] = $result;

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