<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generator Hash Password 💖</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffe4ec, #ffc1e3, #ffd6e0);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
        }
        input[type="text"] {
            width: 80%;
            padding: 12px;
            margin-top: 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            background-color: #ff69b4;
            color: white;
            border: none;
            padding: 12px 30px;
            margin-top: 20px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #ff85c1;
        }
        textarea {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 14px;
            resize: none;
        }
        h2 {
            color: #ff69b4;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>💖 Generator Hash Password 💖</h2>
        <form method="post">
            <input type="text" name="password" placeholder="Masukkan password..." required>
            <br>
            <button type="submit">Generate Hash</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password_plain = $_POST['password'];
            $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);
            echo "<h3>Hasil Hash:</h3>";
            echo "<textarea rows='3' readonly>" . htmlspecialchars($password_hashed) . "</textarea>";
        }
        ?>
    </div>
</body>
</html>
