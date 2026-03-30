<?php
/**
 * /api/login.php
 * Simple login API for Nginx rate limit demo.
 * Protected by Nginx limit_req — this file itself has no rate logic.
 */

header('Content-Type: application/json');
header('X-Powered-By: nginx-rate-demo');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'METHOD_NOT_ALLOWED', 'message' => 'Use POST.']);
    exit;
}

// Parse JSON body
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!$data || !isset($data['username'], $data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'BAD_REQUEST', 'message' => 'username and password required.']);
    exit;
}

$username = trim($data['username']);
$password = $data['password'];

// -----------------------------------------------------------------
// Demo user store — replace with a real DB query + password_verify()
// -----------------------------------------------------------------
$users = [
    'admin'    => password_hash('password123', PASSWORD_BCRYPT),
    'testuser' => password_hash('test1234',    PASSWORD_BCRYPT),
];

// Small artificial delay to simulate real auth (50-100ms)
usleep(random_int(50000, 100000));

if (!isset($users[$username]) || !password_verify($password, $users[$username])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error'   => 'AUTH_FAILED',
        'message' => 'Invalid username or password.',
    ]);
    exit;
}

// Generate a simple demo token (use JWT in production)
$token = base64_encode(random_bytes(32));

http_response_code(200);
echo json_encode([
    'success'  => true,
    'message'  => 'Login successful.',
    'token'    => $token,
    'username' => $username,
    'expires'  => date('c', strtotime('+1 hour')),
]);
