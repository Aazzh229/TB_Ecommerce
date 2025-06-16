<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != 'Penjual') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Hapus child table dulu biar ga error foreign key:
$conn->query("DELETE FROM product_images WHERE product_id = $id");
$conn->query("DELETE FROM product_variants WHERE product_id = $id");
$conn->query("DELETE FROM products WHERE id = $id");

header("Location: index.php");
exit;
?>

<!-- Gaya tampilan pink (TIDAK mengubah kode PHP di atas) -->
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #fce4ec, #f8bbd0);
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
    }
</style>