<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] != "admin") {
    header("Location: index.php");
    exit;
}

// Load PHPMailer via Composer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle assignment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bug_id = $_POST["bug_id"];
    $developer_id = $_POST["developer_id"];

    // Update bug assignment in DB
    $sql = "UPDATE bugs SET assigned_to = $developer_id, status = 'In Progress' WHERE id = $bug_id";

    if ($conn->query($sql) === TRUE) {
        $success_msg = "Bug assigned successfully!";

        // Get developer email and name
        $dev_query = $conn->query("SELECT name, email FROM users WHERE id = $developer_id LIMIT 1");
        $bug_query = $conn->query("SELECT title FROM bugs WHERE id = $bug_id LIMIT 1");

        if ($dev_query && $bug_query && $dev_query->num_rows > 0 && $bug_query->num_rows > 0) {
            $dev = $dev_query->fetch_assoc();
            $bug = $bug_query->fetch_assoc();

            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'aditya.khurana672@gmail.com'; // Replace with your Gmail address
                $mail->Password   = 'avfs bxsu bwvy kwqj';           // Replace with your Gmail App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('your_email@gmail.com', 'Bug Tracker Admin');
                $mail->addAddress($dev['email'], $dev['name']);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'New Bug Assigned: ' . $bug['title'];
                $mail->Body    = "Hi <strong>{$dev['name']}</strong>,<br><br>"
                               . "You have been assigned a new bug: <strong>{$bug['title']}</strong>.<br>"
                               . "Please log in to the system and check the details.<br><br>"
                               . "Regards,<br>Bug Tracker Team";

                $mail->send();
                $success_msg .= " Email sent to developer.";
            } catch (Exception $e) {
                $error_msg = "Bug assigned, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    } else {
        $error_msg = "Error: " . $conn->error;
    }
}

// Fetch unassigned bugs
$bugs = $conn->query("SELECT id, title FROM bugs WHERE assigned_to IS NULL");

// Fetch developers
$developers = $conn->query("SELECT id, name FROM users WHERE role = 'developer'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Bug to Developer</title>
    <style>
        body {
            background-color: #121212;
            color: #f0f0f0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
            background-color: #1e1e1e;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
        label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
        }
        select, input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 8px;
            border: 1px solid #444;
            background-color: #2a2a2a;
            color: #f0f0f0;
            font-size: 15px;
        }
        select:focus, input[type="submit"]:focus {
            outline: none;
            border-color: #00c4ff;
        }
        input[type="submit"] {
            margin-top: 20px;
            background-color: #00c4ff;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #009ac7;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .message.success {
            color: #2ecc71;
        }
        .message.error {
            color: #e74c3c;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #00c4ff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }

        @media screen and (max-width: 600px) {
            form {
                width: 90%;
                padding: 15px;
            }
            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<?php if (isset($success_msg)): ?>
    <div class="message success"><?= $success_msg ?></div>
<?php elseif (isset($error_msg)): ?>
    <div class="message error"><?= $error_msg ?></div>
<?php endif; ?>

<h2>Assign Bug to Developer</h2>

<form method="POST">
    <label for="bug_id">Select Bug:</label>
    <select name="bug_id" id="bug_id" required>
        <option value="">-- Select Bug --</option>
        <?php while($bug = $bugs->fetch_assoc()): ?>
            <option value="<?= $bug['id'] ?>"><?= $bug['title'] ?></option>
        <?php endwhile; ?>
    </select>

    <label for="developer_id">Select Developer:</label>
    <select name="developer_id" id="developer_id" required>
        <option value="">-- Select Developer --</option>
        <?php while($dev = $developers->fetch_assoc()): ?>
            <option value="<?= $dev['id'] ?>"><?= $dev['name'] ?></option>
        <?php endwhile; ?>
    </select>

    <input type="submit" value="Assign Bug">
</form>

<a href="dashboard.php">← Back to Dashboard</a>

</body>
</html>
