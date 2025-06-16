<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Hitung total item di keranjang
$stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE user_id = ? AND is_checked_out = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$cartCount = $row['total'] ?? 0;

// Ambil kategori pakai array (biar aman buat looping)
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

$categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

if ($categoryId > 0) {
    $stmt = $conn->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = ?
    ");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $products = $conn->query("
        SELECT p.*, c.name as category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
    ");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>BEAUTY INSIDE - Produk</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #ffe4ec, #ffc0cb);
    margin: 0;
    padding: 20px;
  }

  .header {
    text-align: center;
    color: #ad1457;
    margin-bottom: 20px;
  }

  .header h1 { margin: 0; font-size: 36px; }
  .header p { margin: 10px 0 0 0; font-size: 18px; }

  .top-menu {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
  }

  .cart-link {
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    color: white;
    margin-left: 10px;
    font-weight: bold;
  }

  .cart-link.green { background-color: #28a745; }
  .cart-link.orange { background-color: #ff9800; }
  .cart-link.red { background-color: #dc3545; }

  .kategori-list {
    margin-bottom: 30px;
    text-align: center;
  }

  .kategori-list a {
    margin: 0 10px;
    padding: 10px 15px;
    background-color: #f8bbd0;
    border-radius: 8px;
    text-decoration: none;
    color: #880e4f;
  }

  .kategori-list a.active {
    background-color: #ec407a;
    color: white;
    font-weight: bold;
  }

  .produk-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
  }

  .produk-item {
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    width: 240px;
    text-align: center;
    padding: 20px;
    transition: transform 0.3s;
  }

  .produk-item:hover { transform: scale(1.05); }

  .produk-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 15px;
  }

  .produk-item h3 {
    color: #ec407a;
    margin: 10px 0;
  }

  .produk-item p {
    margin: 5px 0;
    color: #4e004e;
  }

  .btn-detail {
    background-color: #ec407a;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    margin-top: 10px;
  }

  .btn-detail:hover { background-color: #d81b60; }
</style>
</head>
<body>

<div class="header">
  <h1>BEAUTY INSIDE</h1>
  <p>Your Pretty Product Marketplace ✨💕</p>
</div>

<div class="top-menu">
  <div></div>
  <div>
    <?php if ($role == 'Pembeli'): ?>
        <a href="cart.php" class="cart-link green">Keranjang (<?= $cartCount ?>)</a>
        <a href="transactions.php" class="cart-link orange">Pesanan Saya</a>
    <?php elseif ($role == 'Penjual'): ?>
        <a href="orders_seller.php" class="cart-link orange">Pesanan Masuk</a>
    <?php endif; ?>
    <a href="logout.php" class="cart-link red">Logout</a>
  </div>
</div>

<div class="kategori-list">
  <?php foreach ($categories as $cat): ?>
    <a href="?category_id=<?= $cat['id'] ?>" class="<?= $categoryId === intval($cat['id']) ? 'active' : '' ?>">
      <?= htmlspecialchars($cat['name']) ?>
    </a>
  <?php endforeach; ?>
</div>

<div class="produk-list">
  <?php if ($products->num_rows === 0): ?>
    <p>Tidak ada produk di kategori ini.</p>
  <?php else: ?>
    <?php while($row = $products->fetch_assoc()): ?>
      <div class="produk-item">

        <?php if (!empty($row['image'])): ?>
          <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
        <?php endif; ?>

        <h3><?= htmlspecialchars($row['name']) ?></h3>
        <p>Harga: Rp <?= number_format($row['price'] / 1000, 0, ',', '.') ?></p>
        <p>Kategori: <?= htmlspecialchars($row['category_name']) ?></p>
        <p>Merk: <?= htmlspecialchars($row['merk']) ?></p>
        <p>Deskripsi: <?= nl2br(htmlspecialchars($row['description'])) ?></p>

        <a class="btn-detail" href="product_detail.php?id=<?= $row['id'] ?>">Lihat Detail</a>

        <?php if ($role == 'Penjual'): ?>
          <br><br>
          <a href="edit_product.php?id=<?= $row['id'] ?>">Edit</a> |
          <a href="delete_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin mau hapus produk?')">Hapus</a>
        <?php endif; ?>

      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</div>

<?php if ($role == 'Penjual'): ?>
  <br><a href="create_product.php">+ Tambah Produk</a>
<?php endif; ?>

</body>
</html>
