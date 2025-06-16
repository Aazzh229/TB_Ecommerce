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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .order-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 1200px;
        }

        h2 {
            text-align: center;
            color: #ad1457;
            margin-bottom: 25px;
            font-size: 28px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #f8bbd0;
            padding: 12px;
            text-align: left;
            font-size: 16px;
        }

        th {
            background-color: #fce4ec;
            color: #ad1457;
        }

        a {
            color: #ec407a;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="order-container">
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

    <a class="back-link" href="index.php">Kembali ke Halaman Utama</a>
</div>

</body>
</html>