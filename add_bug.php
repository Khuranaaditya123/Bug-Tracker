<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $created_by = $_SESSION['user_id'];
    $assigned_to = $_POST['assigned_to'];

    $sql = "INSERT INTO bugs (title, description, status, created_by, assigned_to, created_at) 
            VALUES ('$title', '$description', '$status', '$created_by', '$assigned_to', NOW())";

    if ($conn->query($sql) === TRUE) {
        header("Location: all_bugs.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$users_result = $conn->query("SELECT id, username FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bug</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #f1f1f1;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #1e1e1e;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 255, 100, 0.1);
        }

        h2 {
            text-align: center;
            color:  #00c4ff;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            color: #ddd;
            margin-bottom: 8px;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #444;
            font-size: 14px;
            background-color: #2c2c2c;
            color: #f1f1f1;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            background-color:  #00c4ff;
            color: white;
            font-weight: bold;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: block;
            width: 100%;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color:  #00c4ff;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color:  #00c4ff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-link:hover {
            background-color:  #00c4ff;
        }

        @media screen and (max-width: 768px) {
            .container {
                padding: 20px;
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Bug</h2>
    <form action="add_bug.php" method="POST">
        <div class="form-group">
            <label for="title">Bug Title:</label>
            <input type="text" name="title" id="title" required placeholder="Enter bug title">
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" required placeholder="Enter bug description"></textarea>
        </div>

        <div class="form-group">
            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="Open">Open</option>
                <option value="In Progress">In Progress</option>
                <option value="Fixed">Fixed</option>
                <option value="Closed">Closed</option>
            </select>
        </div>

        <div class="form-group">
            <label for="assigned_to">Assign To:</label>
            <select name="assigned_to" id="assigned_to" required>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn">Add Bug</button>
    </form>

    <p><a href="dashboard.php" class="back-link">← Back to Dashboard</a></p>
</div>

</body>
</html>
