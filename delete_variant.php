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
