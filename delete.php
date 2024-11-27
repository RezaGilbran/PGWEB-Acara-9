<?php
$conn = new mysqli("localhost", "root", "", "latihan");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if there is an ID provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Ensure the ID is an integer

    // Prepare a statement to delete the record
    $delete_sql = "DELETE FROM penduduk WHERE id=?";
    $delete_stmt = $conn->prepare($delete_sql);
    if ($delete_stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $delete_stmt->bind_param("i", $id);

    // Execute the deletion
    if ($delete_stmt->execute()) {
        if ($delete_stmt->affected_rows > 0) {
            echo "<script>alert('Data berhasil dihapus'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus data: Tidak ada baris yang dihapus.'); window.location.href = 'index.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal menghapus data: " . $conn->error . "'); window.location.href = 'index.php';</script>";
    }

    $delete_stmt->close();
} else {
    echo "<script>alert('ID tidak diterima'); window.location.href = 'index.php';</script>";
}

$conn->close();
?>
