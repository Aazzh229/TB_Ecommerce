<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $role = "Pembeli"; // default role otomatis Pembeli

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $username, $hashedPassword, $role);
        if ($insert->execute()) {
            echo "<p style='text-align:center; color:green;'>Registrasi berhasil. <a href='login.php'>Klik untuk login</a></p>";
        } else {
            $error = "Gagal mendaftar: " . $insert->error;
        }
        $insert->close();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi</title>
    <style>
        body {
            background-color: #ffe6f0;
            font-family: Arial, sans-serif;
            color: #333;
        }
        .form-container {
            width: 400px;
            margin: 80px auto;
            background-color: #fff0f5;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(255, 192, 203, 0.6);
        }
        h2 {
            text-align: center;
            color: #d63384;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 16px;
            border: 1px solid #ffb6c1;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #ff69b4;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #ff1493;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .login-link a {
            color: #d63384;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Registrasi</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Daftar</button>
    </form>

    <div class="login-link">
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</div>

</body>
</html>