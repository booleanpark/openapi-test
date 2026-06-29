<?php

namespace OpenApiTest\Validation;

use OpenApiTest\Result;

class ResponseValidator
{
    public function __construct(
        private array $spec
    ) {}

    public function validate(string $method, string $path, array $operation, array $response): Result
    {
        $errors = [];

        // 1. Status code
        $expectedStatus = array_key_first($operation['responses'] ?? []);
        if ((string)$response['status'] !== (string)$expectedStatus) {
            $errors[] = "Expected status $expectedStatus, got {$response['status']}";
        }

        // 2. Content-Type
        $contentTypeOk = false;
        foreach ($response['headers'] as $h) {
            if (str_contains($h, 'application/json')) {
                $contentTypeOk = true;
            }
        }

        if (!$contentTypeOk) {
            $errors[] = "Invalid Content-Type (expected application/json)";
        }

        // 3. JSON validation
        $decoded = json_decode($response['body'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $errors[] = "Invalid JSON response";
            return new Result("$method $path", false, $errors);
        }

        // 4. Schema validation (basic)
        $schema = $operation['responses'][$expectedStatus]['content']['application/json']['schema'] ?? null;

        if ($schema) {
            $schemaValidator = new SchemaValidator();
            $schemaErrors = $schemaValidator->validate($schema, $decoded);

            $errors = array_merge($errors, $schemaErrors);
        }

        return new Result("$method $path", empty($errors), $errors);
    }
}