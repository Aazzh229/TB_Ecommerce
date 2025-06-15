<?php
session_start();
include 'db.php';

// Cek apakah user sudah login dan role-nya Penjual
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Penjual') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT t.id, u.username, t.total, t.status, t.payment_method, t.courier, t.shipping_address 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        ORDER BY t.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pesanan Masuk (Penjual)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 10px; }
        th { background-color: #f2f2f2; }
        a { text-decoration: none; color: blue; }
    </style>
</head>
<body>

<h2>Daftar Pesanan Masuk</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Total</th>
        <th>Status</th>
        <th>Metode</th>
        <th>Kurir</th>
        <th>Alamat</th>
        <th>Aksi</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['payment_method'] ?></td>
            <td><?= $row['courier'] ?></td>
            <td><?= htmlspecialchars($row['shipping_address']) ?></td>
            <td><a href="update_status.php?id=<?= $row['id'] ?>">Update Status</a></td>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="index.php">Kembali ke Halaman Utama</a>

</body>
</html>
