<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = md5($_POST["password"]);
    $role = $_POST["role"];

    $sql = "INSERT INTO users (name, email, password, role) 
            VALUES ('$name', '$email', '$password', '$role')";

    if ($conn->query($sql) === TRUE) {
        $success = "✅ Registered successfully. <a href='index.php' style='color:#00BFFF;'>Login</a>";
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Bug Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            background-color: #121212;
            color: #f1f1f1;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .register-container {
            max-width: 450px;
            margin: 60px auto;
            background-color: #1e1e1e;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 255, 255, 0.1);
        }

        h2 {
            text-align: center;
            color: #00BFFF;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #ddd;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-radius: 6px;
            background-color: #2c2c2c;
            color: #f1f1f1;
            font-size: 14px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #00BFFF;
            border: none;
            color: white;
            font-weight: bold;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #009acd;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .success { color: #00FF7F; }
        .error { color: #ff4c4c; }

        @media screen and (max-width: 480px) {
            .register-container {
                padding: 20px;
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>User Registration</h2>

    <?php if (!empty($success)): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="name">Name</label>
        <input type="text" name="name" required>

        <label for="email">Email</label>
        <input type="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" name="password" required>

        <label for="role">Role</label>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="developer">Developer</option>
            <option value="tester">Tester</option>
        </select>

        <input type="submit" value="Register">
        <p style="text-align:center; margin-top: 15px; font-size: 14px;">
    Already registered? <a href="index.php" style="color: #00BFFF; text-decoration: none;">Login here</a>
</p>

    </form>
</div>

</body>
</html>
