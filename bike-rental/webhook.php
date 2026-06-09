<?php
header("Content-Type: application/json");

// Enable error logging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$logFile = __DIR__ . '/webhook_debug.log';
$input = file_get_contents('php://input');
$tx_ref = $_GET['tx_ref'] ?? $_POST['tx_ref'] ?? null;

// Log everything
$logData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'get' => $_GET,
    'post' => $_POST,
    'input' => $input,
    'server' => $_SERVER['REQUEST_URI']
];

file_put_contents($logFile, json_encode($logData) . PHP_EOL, FILE_APPEND);

// Parse tx_ref if it exists
if ($tx_ref && preg_match('/BIKE-(\d+)-(\d+)/', $tx_ref, $matches)) {
    $bike_id = $matches[1];
    $booking_id = $matches[2];
    
    $response = [
        'status' => 'success',
        'message' => 'Webhook processed successfully',
        'bike_id' => $bike_id,
        'booking_id' => $booking_id,
        'tx_ref' => $tx_ref
    ];
    
    file_put_contents($logFile, "SUCCESS: Processed bike $bike_id" . PHP_EOL, FILE_APPEND);
} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid transaction reference format. Expected BIKE-X-XXXXX',
        'tx_ref' => $tx_ref
    ];
    
    file_put_contents($logFile, "ERROR: Invalid tx_ref format: $tx_ref" . PHP_EOL, FILE_APPEND);
}

echo json_encode($response);
?>
