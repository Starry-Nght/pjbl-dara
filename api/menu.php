<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Koneksi database (disamakan dengan setelan di auth.php kamu)
$conn = new mysqli('localhost', 'dara_user', 'dara123', 'dara_catering');

// Cek koneksi
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal',
        'debug' => $conn->connect_error
    ]);
    exit();
}

// Ambil 15 data menu dari tabel yang kita buat tadi
$query = "SELECT * FROM menu";
$result = $conn->query($query);

$daftar_menu = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $daftar_menu[] = [
            'id' => (int)$row['id'],
            'nama_menu' => $row['nama_menu'],
            'kategori' => $row['kategori'],
            'harga' => (int)$row['harga']
        ];
    }
}

// Kirim balik datanya ke aplikasi/frontend berbentuk JSON
echo json_encode([
    'success' => true,
    'message' => 'Berhasil mengambil daftar menu',
    'data' => $daftar_menu
]);

$conn->close();
?>