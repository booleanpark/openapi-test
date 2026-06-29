<?php

namespace OpenApiTest\Spec;

class PathResolver
{
    public static function resolve(string $path): string
    {
        return preg_replace('/\{[^}]+\}/', '1', $path);
    }
}