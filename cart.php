<?php
session_start();
include 'db.php';

// Proteksi login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil isi keranjang dari database (JOIN ke variants dan products)
$stmt = $conn->prepare("
    SELECT ci.id, p.name, v.variant_name, v.price, ci.quantity 
    FROM cart_items ci 
    JOIN product_variants v ON ci.variant_id = v.id 
    JOIN products p ON v.product_id = p.id 
    WHERE ci.user_id = ? AND ci.is_checked_out = 0
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 900px;
        }
        h2 {
            text-align: center;
            color: #ad1457;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f8bbd0;
            color: #333;
        }
        button {
            background-color: #ec407a;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #d81b60;
        }
        a {
            color: #ec407a;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>Keranjang Belanja</h2>

    <?php if ($result->num_rows == 0): ?>
        <p style="text-align:center; font-size:18px;">Keranjang kamu kosong.</p>
    <?php else: ?>
        <form action="checkout_form.php" method="POST">
        <table>
            <tr>
                <th>Pilih</th>
                <th>Nama Produk</th>
                <th>Varian</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>

            <?php 
            $total = 0;
            while($row = $result->fetch_assoc()): 
                $subtotal = $row['price'] * $row['quantity'];
                $total += $subtotal;
            ?>
            <tr>
                <td>
                    <input type="checkbox" name="cart_ids[]" value="<?= $row['id'] ?>">
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['variant_name']) ?></td>
                <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <h3 style="color:#ad1457;">Total: Rp <?= number_format($total, 0, ',', '.') ?></h3>

        <div style="text-align:center; margin-top:30px;">
            <button type="submit">Lanjut Checkout</button>
        </div>
        </form>
    <?php endif; ?>

    <div style="text-align:center; margin-top:30px;">
        <a href="index.php">← Lanjut Belanja</a>
    </div>
</div>

</body>
</html>
