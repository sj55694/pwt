<?php

namespace App;

class CsvEncoder implements EncoderInterface
{
    private string $delimiter;

    public function __construct(string $delimiter = ',')
    {
        $this->delimiter = $delimiter;
    }

    public function supports(string $format): bool
    {
        return in_array($format, ['csv', 'ssv', 'tsv']);
    }

    public function decode(string $data): array
    {
        $lines = explode("\n", trim($data));
        if (empty($lines)) {
            return [];
        }
        $delimiter = $this->detectDelimiter($lines[0]);
        $firstNonEmptyLine = null;
        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $firstNonEmptyLine = $line;
                break;
            }
        }
        if ($firstNonEmptyLine === null) {
            return [];
        }
        $headers = str_getcsv($firstNonEmptyLine, $delimiter, '"', '\\');
        $result = [];
        for ($i = 1; $i < count($lines); $i++) {
            if (trim($lines[$i]) === '') {
                continue;
            }
            $row = str_getcsv($lines[$i], $delimiter, '"', '\\');
            if (count($row) === count($headers)) {
                $result[] = array_combine($headers, $row);
            } elseif (count($row) > 0) {
                $paddedRow = array_pad($row, count($headers), '');
                $result[] = array_combine($headers, $paddedRow);
            }
        }

        return $result;
    }

    public function encode(array $data): string
    {
        if (empty($data)) {
            return '';
        }
        if (!is_array($data[0])) {
            return $this->arrayToCsv($data);
        }
        if (!isset($data[0])) {
            return $this->arrayToCsv($data);
        }
        $headers = array_keys($data[0]);
        $output = $this->arrayToCsv($headers) . "\n";
        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }
            $completeRow = [];
            foreach ($headers as $header) {
                $completeRow[] = $row[$header] ?? '';
            }
            $output .= $this->arrayToCsv($completeRow) . "\n";
        }
        return trim($output);
    }
    private function arrayToCsv(array $row): string
    {
        $parts = [];
        foreach ($row as $value) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            } else {
                $value = (string)$value;
            }
            if (strpos($value, $this->delimiter) !== false ||
                strpos($value, '"') !== false ||
                strpos($value, "\n") !== false) {
                $value = '"' . str_replace('"', '""', $value) . '"';
            }
            $parts[] = $value;
        }
        return implode($this->delimiter, $parts);
    }

    private function detectDelimiter(string $line): string
    {
        return $this->delimiter;
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }
}