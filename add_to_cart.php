<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$error = '';
$success = '';

// 🚨 Tambahkan pengecekan role di sini:
if ($role != 'Pembeli') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['variant_id']) && isset($_POST['quantity'])) {

        $variant_id = $_POST['variant_id'];
        $quantity = $_POST['quantity'];

        $stmt = $conn->prepare("SELECT id, stock FROM product_variants WHERE id = ?");
        $stmt->bind_param("i", $variant_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = "Varian tidak ditemukan.";
        } else {
            $variant = $result->fetch_assoc();
            if ($quantity > $variant['stock']) {
                $error = "Stok tidak mencukupi.";
            } else {
                $stmt = $conn->prepare("INSERT INTO cart_items (user_id, variant_id, quantity, is_checked_out) VALUES (?, ?, ?, 0)");
                $stmt->bind_param("iii", $user_id, $variant_id, $quantity);
                $stmt->execute();

                $success = "Berhasil menambahkan ke keranjang!";
            }
        }
    } else {
        $error = "Data tidak lengkap (variant_id / quantity).";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah ke Keranjang</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 100%;
            max-width: 500px;
        }
        h2 { color: #ad1457; }
        .success { color: green; margin-bottom: 20px; }
        .error { color: red; margin-bottom: 20px; }
        a { color: #ec407a; text-decoration: none; }
        a:hover { text-decoration: underline; }
        button {
            margin-top: 20px; background-color: #ec407a; color: white;
            border: none; padding: 12px 25px; border-radius: 8px;
            font-size: 16px; cursor: pointer; transition: background-color 0.3s ease;
        }
        button:hover { background-color: #d81b60; }
    </style>
</head>
<body>

<div class="container">
    <h2>Tambah ke Keranjang</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
        <a href="javascript:history.back()">← Kembali</a>
    <?php elseif (!empty($success)): ?>
        <div class="success"><?= $success ?></div>
        <a href="cart.php">Lihat Keranjang</a>
    <?php endif; ?>
</div>

</body>
</html>
