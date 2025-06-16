<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$product_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Produk tidak ditemukan.";
    exit;
}

$variants = $conn->query("SELECT * FROM product_variants WHERE product_id = $product_id");
$images = $conn->query("SELECT * FROM product_images WHERE product_id = $product_id");
$has_images = ($images->num_rows > 0);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($product['name']) ?></title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #fce4ec, #ffc0cb);
        margin: 0;
        padding: 40px 0;
    }
    .container {
        background-color: #fff;
        padding: 30px 40px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        max-width: 800px;
        margin: auto;
    }
    h2 {
        color: #ad1457;
        margin-bottom: 20px;
        text-align: center;
    }
    .product-images {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }
    .product-images img {
        width: 250px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .product-info p {
        font-size: 16px;
        margin: 8px 0;
        color: #4e004e;
    }
    select, input[type="number"] {
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
        width: 100%;
    }
    button:hover {
        background-color: #d81b60;
    }
    a {
        display: inline-block;
        margin-top: 20px;
        color: #ec407a;
        text-decoration: none;
        text-align: center;
        width: 100%;
    }
    a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($product['name']) ?></h2>

    <div class="product-images">
        <?php 
        if ($has_images):
            while($img = $images->fetch_assoc()): ?>
                <img src="uploads/<?= htmlspecialchars($img['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php endwhile;
        else: ?>
            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <?php endif; ?>
    </div>

    <div class="product-info">
        <p><strong>Kategori:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
        <p><strong>Merk:</strong> <?= htmlspecialchars($product['merk']) ?></p>
        <p><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($product['description'])) ?></p>
    </div>

    <?php if ($role == 'Pembeli'): ?>
    <form method="POST" action="add_to_cart.php">
        <label for="variant_id">Pilih Varian:</label>
        <select name="variant_id" id="variant_id" required>
            <option value="">-- Pilih Varian --</option>
            <?php while($v = $variants->fetch_assoc()): ?>
                <option value="<?= $v['id'] ?>">
                    <?= htmlspecialchars($v['variant_name']) ?> 
                    (Rp <?= number_format($v['price'] / 1000, 0, ',', '.') ?> | Stok: <?= $v['stock'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label for="quantity">Jumlah:</label>
        <input type="number" name="quantity" id="quantity" value="1" min="1">

        <button type="submit">Tambah ke Keranjang</button>
    </form>
    <?php endif; ?>

    <a href="index.php">← Kembali ke Produk</a>
</div>

</body>
</html>
