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

<form method="POST">
<select name="status">
<?php foreach($statuses as $s): ?>
<option value="<?= $s ?>"><?= ucfirst($s) ?></option>
<?php endforeach; ?>
</select>
<button type="submit">Update</button>
</form>
