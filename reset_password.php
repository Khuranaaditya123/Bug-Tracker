<?php
session_start();
include("config.php");

if (!isset($_SESSION['reset_email'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = md5($_POST['password']);
    $email = $_SESSION['reset_email'];

    $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
    $stmt->bind_param("ss", $pass, $email);
    if ($stmt->execute()) {
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_otp']);
        header("Location: index.php");
        exit;
    } else {    
        $error = "Failed to update password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body { background: #121212; color: #f0f0f0; font-family: Arial; padding: 20px; }
        .box { max-width: 400px; margin: 60px auto; background: #1e1e1e; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,255,255,0.1); }
        input[type=password], input[type=submit] {
            width: 100%; padding: 12px; margin: 10px 0; border: none; border-radius: 6px; background: #2c2c2c; color: #fff;
        }
        input[type=submit] { background-color: #00BFFF; font-weight: bold; }
        input[type=submit]:hover { background-color: #009acd; }
        .error { color: #e74c3c; text-align: center; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="text-align:center;">Create New Password</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Enter new password" required>
            <input type="submit" value="Reset Password">
        </form>
    </div>
</body>
</html>
