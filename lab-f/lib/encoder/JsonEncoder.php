<?php

namespace App;

class JsonEncoder implements EncoderInterface
{
    public function supports(string $format): bool
    {
        return $format === 'json';
    }
    public function decode(string $data): array
    {
        $decoded = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }
        if (is_array($decoded) && isset($decoded[0]) && is_array($decoded[0])) {
            return $decoded;
        }
        if (is_array($decoded) && !isset($decoded[0]) && !empty($decoded)) {
            $isAssociative = false;
            foreach (array_keys($decoded) as $key) {
                if (!is_int($key)) {
                    $isAssociative = true;
                    break;
                }
            }
            if ($isAssociative) {
                return [$decoded];
            } else {
                return $decoded;
            }
        }
        if (empty($decoded) && !is_array($decoded)) {
            return [];
        }
        return is_array($decoded) ? $decoded : [$decoded];
    }

    public function encode(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}