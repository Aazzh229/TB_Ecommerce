<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Penjual') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) { die("Produk tidak ditemukan."); }
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) { die("Produk tidak ditemukan."); }

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'] * 1000;
    $merk = $_POST['merk'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $imageName = $product['image'];

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;

        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, merk=?, description=?, category_id=?, image=? WHERE id=?");
    $stmt->bind_param("sdssisi", $name, $price, $merk, $description, $category_id, $imageName, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal update produk: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Edit Produk</title></head>
<body>
<h2>Edit Produk</h2>

<?php if (!empty($error)): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    Nama Produk: <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br><br>
    Harga: <input type="number" name="price" value="<?= $product['price'] ?>" step="0.01" required><br><br>
    Merk: <input type="text" name="merk" value="<?= htmlspecialchars($product['merk']) ?>" required><br><br>
    Deskripsi: <textarea name="description" rows="5" cols="50"><?= htmlspecialchars($product['description']) ?></textarea><br><br>

    Kategori:
    <select name="category_id" required>
        <?php while($row = $categories->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= $row['id'] == $product['category_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($row['name']) ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    Gambar Saat Ini: <br>
    <?php if (!empty($product['image'])): ?>
        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" width="100"><br><br>
    <?php endif; ?>
    Upload Gambar Baru: <input type="file" name="image"><br><br>

    <button type="submit">Simpan Perubahan</button>
</form>
<br><a href="index.php">← Kembali</a>
</body>
</html>
