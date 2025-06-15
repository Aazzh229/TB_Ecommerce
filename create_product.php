<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Penjual') {
    header("Location: index.php");
    exit;
}

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'] * 1000; 
    $merk = $_POST['merk'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $seller_id = $_SESSION['user_id'];

    // Upload gambar utama
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;
        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    $stmt = $conn->prepare("INSERT INTO products (name, price, category_id, seller_id, merk, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdiisss", $name, $price, $category_id, $seller_id, $merk, $description, $imageName);
    $stmt->execute();
    $product_id = $stmt->insert_id;

    // Upload gallery
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['name'] as $key => $file) {
            $fileName = basename($file);
            $targetPath = $targetDir . $fileName;
            move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $targetPath);

            $conn->query("INSERT INTO product_images (product_id, image_path) VALUES ($product_id, '$fileName')");
        }
    }

    // Insert varian
    foreach ($_POST['variant_name'] as $i => $varian) {
        $variant_price = $_POST['variant_price'][$i] * 1000;
        $stock = $_POST['stock'][$i];

        $stmt = $conn->prepare("INSERT INTO product_variants (product_id, variant_name, price, stock) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isdi", $product_id, $varian, $variant_price, $stock);
        $stmt->execute();
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Tambah Produk</title></head>
<body>
<h2>Tambah Produk Baru</h2>

<form method="POST" enctype="multipart/form-data">
    Nama Produk: <input type="text" name="name" required><br><br>
    Harga (default): <input type="number" name="price" step="0.01" required><br><br>
    Merk: <input type="text" name="merk" required><br><br>
    Deskripsi: <textarea name="description" rows="5" cols="50"></textarea><br><br>

    Gambar Utama Produk: <input type="file" name="image"><br><br>
    Galeri Produk (bisa pilih banyak file): <input type="file" name="gallery[]" multiple><br><br>

    Kategori:
    <select name="category_id" required>
        <?php while($row = $categories->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <h3>Varian Produk:</h3>
    <div id="varian-wrapper">
        <div>
            Varian: <input type="text" name="variant_name[]" required>
            Harga Varian: <input type="number" name="variant_price[]" step="0.01" required>
            Stok: <input type="number" name="stock[]" required>
        </div>
    </div>
    <button type="button" onclick="tambahVarian()">+ Tambah Varian</button><br><br>

    <button type="submit">Tambah Produk</button>
</form>

<script>
function tambahVarian() {
    const wrapper = document.getElementById('varian-wrapper');
    const div = document.createElement('div');
    div.innerHTML = `Varian: <input type="text" name="variant_name[]" required> 
                     Harga Varian: <input type="number" name="variant_price[]" step="0.01" required> 
                     Stok: <input type="number" name="stock[]" required>`;
    wrapper.appendChild(div);
}
</script>

<br><a href="index.php">Kembali ke Daftar Produk</a>
</body>
</html>
