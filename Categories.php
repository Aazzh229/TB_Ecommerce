Tambah Kategori (create_category.php)

<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];

    $conn->query("INSERT INTO categories (name) VALUES ('$name')");
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Category</title></head>
<body>
    <h2>Add Category</h2>
    <form method="post">
        Category Name: <input type="text" name="name" required><br><br>
        <button type="submit">Save</button>
    </form>
</body>
</html>

Tambah Produk (create_product.php) – lengkap dengan dropdown kategori

<?php
include 'db.php';

// Ambil semua kategori
$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Sementara seller_id di-hardcode 1 (nanti pakai session)
    $conn->query("INSERT INTO products (name, price, category_id, seller_id) 
                  VALUES ('$name', '$price', '$category_id', 1)");
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Product</title></head>
<body>
    <h2>Add Product</h2>
    <form method="post">
        Name: <input type="text" name="name" required><br><br>
        Price: <input type="number" step="0.01" name="price" required><br><br>
        Category:
        <select name="category_id" required>
            <option value="">-- Select Category --</option>
            <?php while($row = $categories->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
            <?php endwhile; ?>
        </select><br><br>
        <button type="submit">Save</button>
    </form>
</body>
</html>

Tampilan Produk + Filter Kategori (index.php)

<?php
include 'db.php';

// Ambil kategori
$categories = $conn->query("SELECT * FROM categories");

// Filter kategori jika dipilih
$filter = '';
if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
    $category_id = $_GET['category_id'];
    $filter = "WHERE p.category_id = $category_id";
}

// Ambil produk dengan join kategori
$products = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    $filter
");
?>
<!DOCTYPE html>
<html>
<head><title>Product List</title></head>
<body>
    <h2>Product List</h2>

    <form method="get">
        <label>Filter by Category:</label>
        <select name="category_id" onchange="this.form.submit()">
            <option value="">-- All Categories --</option>
            <?php while($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($category_id) && $category_id == $cat['id']) ? 'selected' : '' ?>>
                    <?= $cat['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <br>
    <table border="1" cellpadding="10">
        <tr>
            <th>Name</th><th>Price</th><th>Category</th>
        </tr>
        <?php while($row = $products->fetch_assoc()): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td>Rp<?= number_format($row['price'], 2, ',', '.') ?></td>
            <td><?= $row['category_name'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="create_product.php">+ Add Product</a> |
    <a href="create_category.php">+ Add Category</a>
</body>
</html>
