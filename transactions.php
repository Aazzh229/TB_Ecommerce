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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            margin: 0;
            padding: 40px 0;
        }
        .container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            max-width: 1000px;
            margin: auto;
        }
        h2 {
            color: #ad1457;
            margin-bottom: 20px;
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #f8bbd0;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f8bbd0;
            color: #880e4f;
        }
        td {
            background-color: #fff0f5;
        }
        a {
            color: #ec407a;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="container">
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

    <a class="back-link" href="index.php">← Kembali ke Halaman Utama</a>
</div>

</body>
</html>
