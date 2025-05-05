<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["user_role"];

$where = "";

if ($role == "developer") {
    $where = "WHERE b.assigned_to = $user_id";
} if ($role == "tester") {
    $where = "WHERE b.reported_by = $user_id";
}


// Fetch bugs with user info
$sql = "SELECT b.*, 
        u1.name AS reported_by, 
        u2.name AS assigned_to_name 
        FROM bugs b
        LEFT JOIN users u1 ON b.reported_by = u1.id
        LEFT JOIN users u2 ON b.assigned_to = u2.id
        $where
        ORDER BY b.created_at DESC";


$result = $conn->query($sql);
if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bugs</title>
    <style>
        body {
            background-color: #121212;
            color: #f0f0f0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            color: #00c4ff;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1e1e1e;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        th {
            background-color: #2c2c2c;
            color: #00c4ff;
        }

        tr:nth-child(even) {
            background-color: #2a2a2a;
        }

        tr:hover {
            background-color: #333;
        }

        a {
            color: #00c4ff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #00c4ff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
        }

        .back-link:hover {
            background-color: #009ac7;
        }

        @media screen and (max-width: 768px) {
            table, th, td {
                font-size: 14px;
            }

            .back-link {
                display: block;
                width: fit-content;
                margin: 20px auto 0;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>All Bugs</h2>

    <table>
        <tr>
            <th>Title</th>
            <th>Reported By</th>
            <th>Assigned To</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Details</th>
        </tr>
        <?php while($bug = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($bug["title"]) ?></td>
            <td><?= htmlspecialchars($bug["reported_by"]) ?></td>
            <td><?= htmlspecialchars($bug["assigned_to_name"] ?? 'Unassigned') ?></td>
            <td><?= htmlspecialchars($bug["status"]) ?></td>
            <td><?= htmlspecialchars($bug["created_at"]) ?></td>
            <td><a href="bug_details.php?bug_id=<?= $bug['id'] ?>">View</a></td>

        </tr>
        <?php endwhile; ?>
    </table>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
