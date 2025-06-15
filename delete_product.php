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
