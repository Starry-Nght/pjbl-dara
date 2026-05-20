<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ambil data JSON
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

// Koneksi database
$conn = new mysqli('localhost', 'dara_user', 'dara123', 'dara_catering');

if ($conn->connect_error) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal',
        'debug' => $conn->connect_error
    ]);

    exit();
}

// Query user berdasarkan email
$stmt = $conn->prepare('SELECT id, email, password, role FROM users WHERE email = ?');

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Prepare statement gagal',
        'debug' => $conn->error
    ]);

    exit();
}

$stmt->bind_param('s', $email);
$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows === 1) {

    $stmt->bind_result($id, $db_email, $db_password, $role);
    $stmt->fetch();

    // Kalau password HASH
    if (password_verify($password,$db_password)) {

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Login berhasil',
            'user' => [
                'id' => $id,
                'email' => $db_email,
                'role' => $role
            ]
        ]);

    } else {

        // Kalau password plaintext biasa
        if ($password === $db_password) {

            http_response_code(200);

            echo json_encode([
                'success' => true,
                'message' => 'Login berhasil',
                'user' => [
                    'id' => $id,
                    'email' => $db_email,
                    'role' => $role
                ]
            ]);

        } else {

            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => 'Password salah'
            ]);
        }
    }

} else {

    http_response_code(401);

    echo json_encode([
        'success' => false,
        'message' => 'Email tidak ditemukan'
    ]);
}

$stmt->close();
$conn->close();
 
?>