<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($product['name']) ?></title>
</head>
<body>

<h2><?= htmlspecialchars($product['name']) ?></h2>

<?php while($img = $images->fetch_assoc()): ?>
    <img src="uploads/<?= htmlspecialchars($img['image_path']) ?>" width="150" style="margin-right:10px;">
<?php endwhile; ?>

<p>Kategori: <?= htmlspecialchars($product['category_name']) ?></p>
<p>Merk: <?= htmlspecialchars($product['merk']) ?></p>
<p>Deskripsi: <?= nl2br(htmlspecialchars($product['description'])) ?></p>

<form method="POST" action="add_to_cart.php">
    Pilih Varian:
    <select name="variant_id" required>
        <option value="">-- Pilih Varian --</option>
        <?php while($v = $variants->fetch_assoc()): ?>
            <option value="<?= $v['id'] ?>">
                <?= htmlspecialchars($v['variant_name']) ?> (Rp <?= number_format($v['price'], 0, ',', '.') ?> | Stok: <?= $v['stock'] ?>)
            </option>
        <?php endwhile; ?>
    </select><br><br>

    Jumlah: <input type="number" name="quantity" value="1" min="1"><br><br>
    <button type="submit">Tambah ke Keranjang</button>
</form>

<a href="index.php">← Kembali ke Produk</a>
</body>
</html>
