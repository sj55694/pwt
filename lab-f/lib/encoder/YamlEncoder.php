<?php

namespace App;

class YamlEncoder implements EncoderInterface
{
    public function supports(string $format): bool
    {
        return $format === 'yaml';
    }

    public function decode(string $data): array
    {
        if (!function_exists('yaml_parse')) {
            return [];
        }
        $decoded = yaml_parse($data);
        if ($decoded === false) {
            return [];
        }
        if (isset($decoded[0]) && is_array($decoded[0])) {
            return $decoded;
        }
        if (is_array($decoded)) {
            return [$decoded];
        }
        return [];
    }

    public function encode(array $data): string
    {
        return yaml_emit($data, YAML_UTF8_ENCODING);
    }
}