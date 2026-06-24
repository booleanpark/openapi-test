<?php
namespace OpenApiTest;

class Result
{
    public function __construct(
        public string $operation,
        public bool $passed,
        public array $errors = []
    ) {}
}
