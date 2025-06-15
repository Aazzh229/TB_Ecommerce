<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != 'Penjual') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM product_variants WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$variant = $stmt->get_result()->fetch_assoc();

if (!$variant) {
    die("Varian tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $variant_name = $_POST['variant_name'];
    $price = $_POST['price'] * 1000;
    $stock = $_POST['stock'];

    $stmt = $conn->prepare("UPDATE product_variants SET variant_name=?, price=?, stock=? WHERE id=?");
    $stmt->bind_param("sdii", $variant_name, $price, $stock, $id);
    $stmt->execute();

    header("Location: edit_product.php?id=" . $variant['product_id']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Varian Produk</title>
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
        .form-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .form-container h2 {
            margin-bottom: 25px;
            color: #ad1457;
        }
        label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #f8bbd0;
            border-radius: 8px;
            font-size: 16px;
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
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Varian Produk</h2>
    <form method="POST">
        <label for="variant_name">Nama Varian:</label>
        <input type="text" name="variant_name" id="variant_name" value="<?= htmlspecialchars($variant['variant_name']) ?>" required>

        <label for="price">Harga:</label>
        <input type="number" name="price" id="price" value="<?= $variant['price'] ?>" step="0.01" required>

        <label for="stock">Stok:</label>
        <input type="number" name="stock" id="stock" value="<?= $variant['stock'] ?>" required>

        <button type="submit">Update</button>
    </form>
</div>

</body>
</html>
