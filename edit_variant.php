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

<form method="POST">
    Nama Varian: <input type="text" name="variant_name" value="<?= htmlspecialchars($variant['variant_name']) ?>" required><br>
    Harga: <input type="number" name="price" value="<?= $variant['price'] ?>" step="0.01" required><br>
    Stok: <input type="number" name="stock" value="<?= $variant['stock'] ?>" required><br>
    <button type="submit">Update</button>
</form>
