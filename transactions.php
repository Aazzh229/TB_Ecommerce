<?php
session_start();
include 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Query sesuai role
if ($role === 'Penjual') {
    $sql = "SELECT t.id, u.username, t.total, t.status, t.payment_method, t.courier, t.shipping_address
            FROM transactions t
            JOIN users u ON t.user_id = u.id";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT t.id, u.username, t.total, t.status, t.payment_method, t.courier, t.shipping_address
            FROM transactions t
            JOIN users u ON t.user_id = u.id
            WHERE t.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pesanan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 10px; }
        th { background-color: #f2f2f2; }
        a { text-decoration: none; color: blue; }
    </style>
</head>
<body>

<h2>Daftar Pesanan</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Total</th>
        <th>Status</th>
        <th>Metode</th>
        <th>Kurir</th>
        <th>Alamat</th>
        <?php if ($role === 'Penjual') echo "<th>Aksi</th>"; ?>
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

            <?php if ($role === 'Penjual'): ?>
                <td><a href="update_status.php?id=<?= $row['id'] ?>">Update Status</a></td>
            <?php endif; ?>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="index.php">Kembali ke Halaman Utama</a>

</body>
</html>
