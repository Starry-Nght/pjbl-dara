<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['email']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email dan password diperlukan'
    ]);
    exit();
}

$email = trim($input['email']);
$password = trim($input['password']);

// Debug: log the attempt
error_log("Login attempt: email=$email, password=$password");

// Connect to MySQL
$conn = new mysqli('localhost', 'dara_user', 'dara123', 'dara_catering');

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal terhubung ke database'
    ]);
    exit();
}

// Query user
$stmt = $conn->prepare('SELECT id, email, role FROM users WHERE email = ? AND password = ?');
$stmt->bind_param('ss', $email, $password);
$stmt->execute();
$result = $stmt->get_result();

// Debug: log result
error_log("Query result rows: " . $result->num_rows);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Login berhasil',
        'user' => $user
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Email atau kata sandi salah'
    ]);
}

$stmt->close();
$conn->close();
?>
