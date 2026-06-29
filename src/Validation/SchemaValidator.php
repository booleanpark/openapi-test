<?php

namespace OpenApiTest\Validation;

class SchemaValidator
{
    public function validate(array $schema, mixed $data, string $path = ''): array
    {
        $errors = [];

        $type = $schema['type'] ?? null;

        // NULL handling
        if ($data === null) {
            if (($schema['nullable'] ?? false) === true) {
                return [];
            }
            return [$path . " is null"];
        }

        // TYPE validation
        if ($type && !$this->checkType($type, $data)) {
            $errors[] = "$path expected $type";
            return $errors;
        }

        // OBJECT validation
        if ($type === 'object') {

            $required = $schema['required'] ?? [];
            $props = $schema['properties'] ?? [];

            foreach ($required as $field) {
                if (!array_key_exists($field, $data)) {
                    $errors[] = "$path.$field is required";
                }
            }

            foreach ($props as $key => $propSchema) {
                if (array_key_exists($key, $data)) {
                    $errors = array_merge(
                        $errors,
                        $this->validate($propSchema, $data[$key], $path . ".$key")
                    );
                }
            }
        }

        // ARRAY validation
        if ($type === 'array') {
            $itemsSchema = $schema['items'] ?? null;

            if ($itemsSchema) {
                foreach ($data as $i => $item) {
                    $errors = array_merge(
                        $errors,
                        $this->validate($itemsSchema, $item, $path . "[$i]")
                    );
                }
            }
        }

        return $errors;
    }

    private function checkType(string $type, mixed $data): bool
    {
        return match ($type) {
            'string' => is_string($data),
            'integer' => is_int($data),
            'boolean' => is_bool($data),
            'number' => is_int($data) || is_float($data),
            'array' => is_array($data),
            'object' => is_array($data),
            default => true,
        };
    }
}