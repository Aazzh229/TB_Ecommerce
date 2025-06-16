<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != 'Penjual') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE transactions SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    header("Location: transactions.php");
    exit;
}

$statuses = ['paid', 'shipped', 'completed', 'cancelled'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Status Pesanan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            margin: 0;
            padding: 40px 0;
        }
        .container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            margin: auto;
            text-align: center;
        }
        h2 {
            color: #ad1457;
            margin-bottom: 25px;
        }
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
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
            width: 100%;
        }
        button:hover {
            background-color: #d81b60;
        }
        a {
            display: block;
            margin-top: 20px;
            color: #ec407a;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Update Status Pesanan</h2>

    <form method="POST">
        <select name="status" required>
            <?php foreach($statuses as $s): ?>
                <option value="<?= $s ?>"><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Update</button>
    </form>

    <a href="transactions.php">← Kembali ke Daftar Pesanan</a>
</div>

</body>
</html>
