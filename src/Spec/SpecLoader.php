<?php

namespace OpenApiTest\Spec;

class SpecLoader
{
    public function load(string $file): array
    {
        $content = file_get_contents($file);

        return json_decode($content, true);
    }
}