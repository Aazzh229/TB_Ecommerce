
<?php
session_start();
include 'db.php';

// Proteksi halaman
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

include 'product.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>Product List</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #fce4ec, #f8bbd0);
    margin: 20px;
  }

  .header {
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  h1 {
    color: #ad1457;
  }

  .cart-link {
    background-color: #ec407a;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    margin-left: 10px;
    transition: background-color 0.3s ease;
  }

  .cart-link:hover {
    background-color: #d81b60;
  }

  .kategori-list {
    margin-bottom: 20px;
  }

  .kategori-list a {
    margin-right: 10px;
    padding: 8px 12px;
    background-color: #f8bbd0;
    border-radius: 4px;
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
    gap: 20px;
  }

  .produk-item {
    border: 1px solid #f8bbd0;
    background-color: #fff;
    padding: 15px;
    border-radius: 5px;
    width: 220px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
  }

  .produk-item h3 {
    margin: 0 0 10px 0;
    color: #ad1457;
  }

  .produk-item p {
    margin: 5px 0;
    color: #4e004e;
  }

  input[type="number"] {
    width: 60px;
    padding: 6px;
    border: 1px solid #f8bbd0;
    border-radius: 5px;
  }

  button {
    background-color: #ec407a;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    margin-top: 10px;
    transition: background-color 0.3s ease;
  }

  button:hover {
    background-color: #d81b60;
  }

  a {
    color: #ec407a;
    text-decoration: none;
  }

  a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>

<div class="header">
  <h1>Produk</h1>

  <div>
    <?php if ($role == 'Pembeli'): ?>
        <a href="cart.php" class="cart-link">Keranjang (<?= $cartCount ?>)</a>
        <a href="transactions.php" class="cart-link" style="background-color:#ff9800;">Pesanan Saya</a>
    <?php elseif ($role == 'Penjual'): ?>
        <a href="orders_seller.php" class="cart-link" style="background-color:#ff9800;">Pesanan Masuk</a>
    <?php endif; ?>

    <a href="logout.php" class="cart-link" style="background-color:#dc3545;">Logout</a>
  </div>
</div>

<div class="kategori-list">
  <a href="index.php" class="<?= $categoryId === 0 ? 'active' : '' ?>">Semua</a>
  <?php while ($cat = $categories->fetch_assoc()): ?>
    <a href="?category_id=<?= $cat['id'] ?>" class="<?= $categoryId === intval($cat['id']) ? 'active' : '' ?>">
      <?= htmlspecialchars($cat['name']) ?>
    </a>
  <?php endwhile; ?>
</div>

<div class="produk-list">
  <?php if ($products->num_rows === 0): ?>
    <p>Tidak ada produk di kategori ini.</p>
  <?php else: ?>
    <?php while($row = $products->fetch_assoc()): ?>
      <div class="produk-item">

        <?php if (!empty($row['image'])): ?>
          <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="150"><br>
        <?php endif; ?>

        <h3><a href="product_detail.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a></h3>
        <p>Harga: Rp <?= number_format($row['price'], 0, ',', '.') ?></p>
        <p>Kategori: <?= htmlspecialchars($row['category_name']) ?></p>
        <p>Merk: <?= htmlspecialchars($row['merk']) ?></p>
        <p>Deskripsi: <?= nl2br(htmlspecialchars($row['description'])) ?></p>

        <form method="POST" action="add_to_cart.php" style="margin-top:10px;">
          <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
          <input type="number" name="quantity" value="1" min="1">
          <button type="submit">Tambah ke Keranjang</button>
        </form>

        <?php if ($role == 'Penjual'): ?>
          <br>
          <a href="edit_product.php?id=<?= $row['id'] ?>">Edit</a> |
          <a href="delete_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin mau hapus produk?')">Hapus</a>
        <?php endif; ?>

      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</div>

<?php if ($role == 'Penjual'): ?>
  <br>
  <a href="create_product.php">+ Tambah Produk</a>
<?php endif; ?>

</body>
</html>