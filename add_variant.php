<h3>Daftar Varian:</h3>

<table border="1" cellpadding="5" cellspacing="0">
<tr>
    <th>Varian</th><th>Harga</th><th>Stok</th><th>Aksi</th>
</tr>
<?php
$variant_result = $conn->query("SELECT * FROM product_variants WHERE product_id = $id");
while ($variant = $variant_result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($variant['variant_name']) ?></td>
    <td>Rp <?= number_format($variant['price'], 0, ',', '.') ?></td>
    <td><?= $variant['stock'] ?></td>
    <td>
        <a href="edit_variant.php?id=<?= $variant['id'] ?>">Edit</a> | 
        <a href="delete_variant.php?id=<?= $variant['id'] ?>&product_id=<?= $id ?>" onclick="return confirm('Hapus varian ini?')">Hapus</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<h3>Tambah Varian Baru:</h3>
<form method="POST" action="add_variant.php">
    <input type="hidden" name="product_id" value="<?= $id ?>">
    Nama Varian: <input type="text" name="variant_name" required>
    Harga: <input type="number" name="price" step="0.01" required>
    Stok: <input type="number" name="stock" required>
    <button type="submit">Tambah</button>
</form>
