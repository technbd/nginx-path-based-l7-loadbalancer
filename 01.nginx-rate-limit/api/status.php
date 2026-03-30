<?php
/**
 * /api/status.php
 * Health-check endpoint — also rate limited by Nginx.
 */

header('Content-Type: application/json');

echo json_encode([
    'status'    => 'ok',
    'server'    => 'nginx-rate-demo',
    'php'       => PHP_VERSION,
    'timestamp' => date('c'),
    'rate_limit' => [
        'zone'  => 'login_limit',
        'rate'  => '5r/m',
        'burst' => 2,
    ],
]);
