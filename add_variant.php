<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Varian</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 800px;
        }
        h3 {
            text-align: center;
            color: #ad1457;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f8bbd0;
            color: #333;
        }
        a {
            color: #ec407a;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        form {
            margin-top: 20px;
        }
        input[type="text"], input[type="number"] {
            width: 95%;
            padding: 10px;
            margin: 5px 0 15px;
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
        }
        button:hover {
            background-color: #d81b60;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>Daftar Varian</h3>

    <table>
        <tr>
            <th>Varian</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
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

    <h3>Tambah Varian Baru</h3>

    <form method="POST" action="add_variant.php">
        <input type="hidden" name="product_id" value="<?= $id ?>">
        Nama Varian: <input type="text" name="variant_name" required><br>
        Harga: <input type="number" name="price" step="0.01" required><br>
        Stok: <input type="number" name="stock" required><br>
        <button type="submit">Tambah</button>
    </form>
</div>

</body>
</html>
