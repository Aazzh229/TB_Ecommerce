<?php
$host = "localhost";
$user = "root";
$pass = "syifaa_1402";
$dbname = 'ecommerce_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

if ($categoryId > 0) {
    $filter = "WHERE p.category_id = ?";
    $stmt = $conn->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        $filter
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