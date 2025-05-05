<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] != "developer") {
    header("Location: index.php");
    exit;
}

$developer_id = $_SESSION["user_id"];

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["bug_id"], $_POST["status"])) {
    $bug_id = $_POST["bug_id"];
    $status = $_POST["status"];
    
    $sql = "UPDATE bugs SET status = '$status' WHERE id = $bug_id AND assigned_to = $developer_id";
    $conn->query($sql);
}

// Fetch bugs assigned to this developer
$sql = "SELECT * FROM bugs WHERE assigned_to = $developer_id";
$result = $conn->query($sql);
if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Assigned Bugs</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1e1e1e;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            border-bottom: 1px solid #333;
            text-align: left;
        }
        th {
            background-color: #2c2c2c;
            color: #00c4ff;
        }
        td form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        select {
            background-color: #2a2a2a;
            color: #f0f0f0;
            border: 1px solid #444;
            border-radius: 6px;
            padding: 8px;
        }
        input[type="submit"] {
            background-color: #00c4ff;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        input[type="submit"]:hover {
            background-color: #009ac7;
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

        @media screen and (max-width: 768px) {
            table, th, td {
                font-size: 14px;
            }
            td form {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>

<h2>Your Assigned Bugs</h2>

<table>
    <tr>
        <th>Bug Title</th>
        <th>Description</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row["title"]) ?></td>
        <td><?= htmlspecialchars($row["description"]) ?></td>
        <td><?= htmlspecialchars($row["status"]) ?></td>
        <td>
            <form method="POST">
                <input type="hidden" name="bug_id" value="<?= $row["id"] ?>">
                <select name="status" required>
                    <option value="In Progress" <?= $row["status"] == "In Progress" ? "selected" : "" ?>>In Progress</option>
                    <option value="Fixed" <?= $row["status"] == "Fixed" ? "selected" : "" ?>>Fixed</option>
                    <option value="Closed" <?= $row["status"] == "Closed" ? "selected" : "" ?>>Closed</option>
                </select>
                <input type="submit" value="Update">
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<a href="dashboard.php">← Back to Dashboard</a>

</body>
</html>
