<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $variant_id = $_POST['variant_id'];
    $quantity = $_POST['quantity'];

    // Cek apakah variant tersedia
    $stmt = $conn->prepare("SELECT id, stock FROM product_variants WHERE id = ?");
    $stmt->bind_param("i", $variant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "Varian tidak ditemukan.";
        exit;
    }

    $variant = $result->fetch_assoc();
    if ($quantity > $variant['stock']) {
        echo "Stok tidak mencukupi.";
        exit;
    }

    // Tambahkan ke cart_items
    $stmt = $conn->prepare("INSERT INTO cart_items (user_id, variant_id, quantity, is_checked_out) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("iii", $user_id, $variant_id, $quantity);
    $stmt->execute();

    header("Location: cart.php");
    exit;
}
?>
