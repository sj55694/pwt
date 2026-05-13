<?php
require_once 'lib/encoder/encoderInterface.php';
require_once 'lib/encoder/CsvEncoder.php';
require_once 'lib/encoder/JsonEncoder.php';
require_once 'lib/encoder/YamlEncoder.php';

use App\CsvEncoder;
use App\JsonEncoder;
use App\YamlEncoder;

$output = '';
$error = '';

$defaultData = [
    'input_data' => '',
    'input_format' => 'csv',
    'output_format' => 'json'
];

if (isset($_COOKIE['converter_data'])) {
    $savedData = json_decode($_COOKIE['converter_data'], true);
    if (is_array($savedData)) {
        $defaultData = array_merge($defaultData, $savedData);
    }
}

$inputData = $_POST['input_data'] ?? $defaultData['input_data'];
$inputFormat = $_POST['input_format'] ?? $defaultData['input_format'];
$outputFormat = $_POST['output_format'] ?? $defaultData['output_format'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cookieData = [
        'input_data' => $inputData,
        'input_format' => $inputFormat,
        'output_format' => $outputFormat
    ];
    setcookie('converter_data', json_encode($cookieData), time() + 30 * 24 * 3600, '/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($inputData)) {
        $error = 'Proszę wprowadzić dane wejściowe.';
    } else {
        try {
            $decodedData = null;
            
            if ($inputFormat === 'csv') {
                $encoder = new CsvEncoder(',');
                $decodedData = $encoder->decode($inputData);
            } elseif ($inputFormat === 'ssv') {
                $encoder = new CsvEncoder(';');
                $decodedData = $encoder->decode($inputData);
            } elseif ($inputFormat === 'tsv') {
                $encoder = new CsvEncoder("\t");
                $decodedData = $encoder->decode($inputData);
            } elseif ($inputFormat === 'json') {
                $encoder = new JsonEncoder();
                $decodedData = $encoder->decode($inputData);
            } elseif ($inputFormat === 'yaml') {
                $encoder = new YamlEncoder();
                $decodedData = $encoder->decode($inputData);
            }
            
            if ($decodedData === null || (isset($decodedData['error']) && $decodedData['error'])) {
                $error = 'Błąd przy dekodowaniu danych wejściowych. Sprawdź format.';
            } else if (empty($decodedData)) {
                $error = 'Nie można przeanalizować danych wejściowych.';
            } else {

                if ($outputFormat === 'csv') {
                    $encoder = new CsvEncoder(',');
                    $output = $encoder->encode($decodedData);
                } elseif ($outputFormat === 'ssv') {
                    $encoder = new CsvEncoder(';');
                    $output = $encoder->encode($decodedData);
                } elseif ($outputFormat === 'tsv') {
                    $encoder = new CsvEncoder("\t");
                    $output = $encoder->encode($decodedData);
                } elseif ($outputFormat === 'json') {
                    $encoder = new JsonEncoder();
                    $output = $encoder->encode($decodedData);
                } elseif ($outputFormat === 'yaml') {
                    $encoder = new YamlEncoder();
                    $output = $encoder->encode($decodedData);
                }
            }
        } catch (Exception $e) {
            $error = 'Błąd: ' . $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jakub Sibora (55694) - PTW LAB F</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>


    <form method="POST">
        <div class="form-group">
            <label for="input_data">Dane wejściowe:</label>
            <textarea id="input_data" name="input_data" required><?php echo htmlspecialchars($inputData); ?></textarea>
        </div>
        
        <div class="format-row">
            <div class="form-group">
                <label for="input_format">Format wejściowy:</label>
                <select id="input_format" name="input_format">
                    <option value="csv" <?php echo $inputFormat === 'csv' ? 'selected' : ''; ?>>CSV (przecinek)</option>
                    <option value="ssv" <?php echo $inputFormat === 'ssv' ? 'selected' : ''; ?>>SSV (średnik)</option>
                    <option value="tsv" <?php echo $inputFormat === 'tsv' ? 'selected' : ''; ?>>TSV (tabulator)</option>
                    <option value="json" <?php echo $inputFormat === 'json' ? 'selected' : ''; ?>>JSON</option>
                    <option value="yaml" <?php echo $inputFormat === 'yaml' ? 'selected' : ''; ?>>YAML</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="output_format">Format wyjściowy:</label>
                <select id="output_format" name="output_format">
                    <option value="csv" <?php echo $outputFormat === 'csv' ? 'selected' : ''; ?>>CSV (przecinek)</option>
                    <option value="ssv" <?php echo $outputFormat === 'ssv' ? 'selected' : ''; ?>>SSV (średnik)</option>
                    <option value="tsv" <?php echo $outputFormat === 'tsv' ? 'selected' : ''; ?>>TSV (tabulator)</option>
                    <option value="json" <?php echo $outputFormat === 'json' ? 'selected' : ''; ?>>JSON</option>
                    <option value="yaml" <?php echo $outputFormat === 'yaml' ? 'selected' : ''; ?>>YAML</option>
                </select>
            </div>
        </div>
        
        <button type="submit">Konwertuj</button>
    </form>
    
    <?php if ($output || $error): ?>
        <div class="output-section">
            <h2>Dane wyjściowe:</h2>
            <pre><?php echo htmlspecialchars($output); ?></pre>
        </div>
    <?php endif; ?>
</body>