<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Akun tidak ditemukan. Silakan register terlebih dahulu.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Beauty Inside</title>
</head>
<body style="
    font-family: Arial, sans-serif;
    margin: 0;
    background: linear-gradient(135deg, #ffdce0, #ffeef3, #fff5f7);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
">

    <div style="
        background-color: rgba(255, 240, 245, 0.95);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        width: 350px;
    ">

        <!-- Nama Aplikasi -->
        <div style="text-align: center; font-family: 'Segoe Script', cursive; font-size: 28px; color: #d45a85; margin-bottom: 10px;">
            Beauty Inside
        </div>

        <!-- Judul Form -->
        <h2 style="text-align: center; color: #333;">Login</h2>

        <!-- Pesan Error -->
        <?php if (!empty($error)): ?>
            <p style="color: red; text-align: center;"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Form Login -->
        <form method="POST" style="display: flex; flex-direction: column;">
            <label>Username:</label>
            <input type="text" name="username" required style="padding: 8px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ccc;">

            <label>Password:</label>
            <input type="password" name="password" required style="padding: 8px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #ccc;">

            <button type="submit" style="padding: 10px; background-color: #f78fb3; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Login</button>
        </form>

        <!-- Link ke Register -->
        <p style="text-align: center; margin-top: 15px;">
            Belum punya akun?
            <a href="register.php" style="color: #d45a85; text-decoration: none; font-weight: bold;">Daftar disini</a>
        </p>

    </div>

</body>
</html>
