<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] != "tester") {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $severity = $_POST["severity"];
    $reported_by = $_SESSION["user_id"];

    $sql = "INSERT INTO bugs (title, description, severity, reported_by) 
            VALUES ('$title', '$description', '$severity', $reported_by)";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:lightgreen;'>✅ Bug reported successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report a Bug</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #f1f1f1;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #1e1e1e;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 255, 255, 0.05);
        }
        h2 {
            text-align: center;
            color: #00BFFF;
            margin-bottom: 25px;
        }
        label {
            font-size: 15px;
            color: #ccc;
        }
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #2c2c2c;
            border: none;
            border-radius: 6px;
            color: #f1f1f1;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #00BFFF;
            padding: 12px;
            border: none;
            color: white;
            font-weight: bold;
            font-size: 15px;
            border-radius: 6px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #009acd;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #00BFFF;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Report a Bug</h2>
    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="5" required></textarea>

        <label for="severity">Severity:</label>
        <select name="severity" id="severity" required>
            <option value="">--Select Severity--</option>
            <option value="Critical">Critical</option>
            <option value="Severe">Severe</option>
            <option value="Moderate">Moderate</option>
            <option value="Minor">Minor</option>
        </select>

        <input type="submit" value="Report Bug">
    </form>

    <div class="back-link">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
