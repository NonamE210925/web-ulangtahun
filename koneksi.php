<?php


$mysqli = new mysqli("localhost", "noname", "210925", "ultah");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
// Memeriksa koneksi
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $pesan = $_POST['pesan'];

    // Pemeriksaan apakah nama sudah ada dalam database
    $checkQuery = "SELECT nama FROM pesan WHERE nama = '$nama'";
    $checkResult = $mysqli->query($checkQuery);
    
    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Nama sudah ada dalam database, pesan tidak bisa ditambahkan.'); window.location.href = 'index.php  ';</script>";
    } else {
        // Proses unggah foto dengan nama acak
        $foto_name = uniqid() . "_" . $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_path = "uploads/" . $foto_name;
        move_uploaded_file($foto_tmp, $foto_path);

        $query = "INSERT INTO pesan (nama, pesan, foto) VALUES ('$nama', '$pesan', '$foto_path')";
        if ($mysqli->query($query) === true) {
            echo "<script>alert('Pesan berhasil dikirim.');window.location.href = 'index.php    ';</script>";
        } else {
            echo "<script>alert('Error: Pesan gagal dikirim.');window.location.href = 'index.php    ';</script>";
        }
    }
}
?>

