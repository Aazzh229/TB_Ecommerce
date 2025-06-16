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

    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['name'] as $key => $file) {
            $fileName = basename($file);
            $targetPath = $targetDir . $fileName;
            move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $targetPath);
            $conn->query("INSERT INTO product_images (product_id, image_path) VALUES ($product_id, '$fileName')");
        }
    }

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
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
        }
        .form-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            max-width: 700px;
            width: 100%;
        }
        h2, h3 {
            color: #ad1457;
            text-align: center;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #f8bbd0;
            border-radius: 8px;
            font-size: 15px;
        }
        button {
            background-color: #ec407a;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #d81b60;
        }
        a {
            display: block;
            margin-top: 20px;
            color: #ec407a;
            text-align: center;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        #varian-wrapper > div {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Tambah Produk Baru</h2>
    <form method="POST" enctype="multipart/form-data">
        Nama Produk: <input type="text" name="name" required>
        Harga (default): <input type="number" name="price" step="0.01" required>
        Merk: <input type="text" name="merk" required>
        Deskripsi: <textarea name="description" rows="5"></textarea>
        Gambar Utama Produk: <input type="file" name="image">
        Galeri Produk: <input type="file" name="gallery[]" multiple>

        Kategori:
        <select name="category_id" required>
            <?php while($row = $categories->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select>

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
    <a href="index.php">← Kembali ke Daftar Produk</a>
</div>

<script>
    function tambahVarian() {
        const wrapper = document.getElementById('varian-wrapper');
        const div = document.createElement('div');
        div.innerHTML = `
            Varian: <input type="text" name="variant_name[]" required>
            Harga Varian: <input type="number" name="variant_price[]" step="0.01" required>
            Stok: <input type="number" name="stock[]" required>
        `;
        wrapper.appendChild(div);
    }
</script>
</body>
</html>
