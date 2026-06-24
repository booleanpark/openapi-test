<?php
namespace OpenApiTest;

class OpenApiTest
{
    public static function run(string $specFile, string $baseUrl): array
    {
        return (new Runner())->run($specFile, $baseUrl);
    }
}
