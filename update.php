<?php
$conn = new mysqli("localhost", "root", "", "latihan");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek apakah ada ID yang dikirimkan melalui GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data berdasarkan ID
    $sql = "SELECT * FROM penduduk WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
} else {
    echo "<script>alert('ID tidak ditemukan!'); window.location.href = 'index.php';</script>";
    exit();
}

// Proses update data jika form dikirimkan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $kecamatan = $_POST['kecamatan'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $luas = $_POST['luas'];
    $jumlah_penduduk = $_POST['jumlah_penduduk'];

    $update_sql = "UPDATE penduduk SET kecamatan=?, latitude=?, longitude=?, luas=?, jumlah_penduduk=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssddii", $kecamatan, $latitude, $longitude, $luas, $jumlah_penduduk, $id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Data berhasil diupdate'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data');</script>";
    }

    $update_stmt->close();
}

$conn->close();
?>

<!-- Form Update -->
<!DOCTYPE html>
<html>

<head>
    <title>Update Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #title {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 id="title">Update Data</h2>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
            <div class="mb-3">
                <label for="kecamatan" class="form-label">Kecamatan</label>
                <input type="text" class="form-control" id="kecamatan" name="kecamatan" value="<?php echo $data['kecamatan']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="latitude" class="form-label">Latitude</label>
                <input type="text" class="form-control" id="latitude" name="latitude" value="<?php echo $data['latitude']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="longitude" class="form-label">Longitude</label>
                <input type="text" class="form-control" id="longitude" name="longitude" value="<?php echo $data['longitude']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="luas" class="form-label">Luas</label>
                <input type="text" class="form-control" id="luas" name="luas" value="<?php echo $data['luas']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="jumlah_penduduk" class="form-label">Jumlah Penduduk</label>
                <input type="number" class="form-control" id="jumlah_penduduk" name="jumlah_penduduk" value="<?php echo $data['jumlah_penduduk']; ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>

</html>
