<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != 'Penjual') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$product_id = $_GET['product_id'];

$stmt = $conn->prepare("DELETE FROM product_variants WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: edit_product.php?id=$product_id");
exit;
?>

<!-- Tambahan tampilan pink TANPA ubah kode PHP -->
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #fce4ec, #f8bbd0);
        height: 100vh;
        margin: 0;
    }
</style>
