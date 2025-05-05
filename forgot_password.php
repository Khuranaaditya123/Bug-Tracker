<?php
session_start();
include("config.php");
require 'vendor/autoload.php'; // Composer autoload
use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $otp = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $otp;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'aditya.khurana672@gmail.com'; // replace
            $mail->Password = 'avfs bxsu bwvy kwqj';    // replace
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'Bug Tracker');
            $mail->addAddress($email);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body = "Your OTP is: $otp";

            $mail->send();
            header("Location: verify_otp.php");
            exit;
        } catch (Exception $e) {
            $error = "Failed to send email. Try again.";
        }
    } else {
        $error = "Email not found!";
    }
}
?>

<!-- Basic dark-themed form to enter email -->
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body { background: #121212; color: #f0f0f0; font-family: Arial; padding: 20px; }
        .box { max-width: 400px; margin: 60px auto; background: #1e1e1e; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,255,255,0.1); }
        input[type=email], input[type=submit] {
            width: 100%; padding: 12px; margin: 10px 0; border: none; border-radius: 6px; background: #2c2c2c; color: #fff;
        }
        input[type=submit] { background-color: #00BFFF; font-weight: bold; }
        input[type=submit]:hover { background-color: #009acd; }
        .error { color: #e74c3c; text-align: center; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="text-align:center;">Forgot Password</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your registered email" required>
            <input type="submit" value="Send OTP">
        </form>
    </div>
</body>
</html>
