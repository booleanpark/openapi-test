<?php

namespace OpenApiTest;

class Result
{
    public function __construct(
        public string $operation,
        public bool $success,
        public array $errors = []
    ) {}
}