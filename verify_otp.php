<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = $_POST['otp'];
    if ($entered_otp == $_SESSION['reset_otp']) {
        header("Location: reset_password.php");
        exit;
    } else {
        $error = "Incorrect OTP!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <style>
        body { background: #121212; color: #f0f0f0; font-family: Arial; padding: 20px; }
        .box { max-width: 400px; margin: 60px auto; background: #1e1e1e; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,255,255,0.1); }
        input[type=text], input[type=submit] {
            width: 100%; padding: 12px; margin: 10px 0; border: none; border-radius: 6px; background: #2c2c2c; color: #fff;
        }
        input[type=submit] { background-color: #00BFFF; font-weight: bold; }
        input[type=submit]:hover { background-color: #009acd; }
        .error { color: #e74c3c; text-align: center; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="text-align:center;">Enter OTP</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="otp" placeholder="Enter the OTP sent to your email" required>
            <input type="submit" value="Verify OTP">
        </form>
    </div>
</body>
</html>
