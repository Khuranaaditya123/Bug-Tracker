<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

// Get bug_id from URL (GET request)
$bug_id = isset($_GET['bug_id']) ? $_GET['bug_id'] : null;

if ($bug_id) {
    // Fetch bug details from database
    $sql = "SELECT title, description, status FROM bugs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bug_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $bug = $result->fetch_assoc();
    } else {
        echo "Bug not found!";
        exit;
    }
} else {
    echo "No bug ID provided!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bug Details</title>
    <style>
        body {
            background-color: #121212;
            color: #f0f0f0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
        }
        .bug-details {
            max-width: 800px;
            margin: 0 auto;
            background-color: #1e1e1e;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.6);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #00c4ff;
        }
        p {
            font-size: 16px;
            margin: 10px 0;
            line-height: 1.6;
        }
        p strong {
            color: #00c4ff;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #00c4ff;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .bug-details {
                width: 90%;
                padding: 20px;
            }
            h2 {
                font-size: 22px;
            }
            p {
                font-size: 15px;
            }
        }

        @media screen and (max-width: 480px) {
            .bug-details {
                width: 100%;
                padding: 15px;
            }
            h2 {
                font-size: 20px;
            }
            p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="bug-details">
    <h2>Bug Details</h2>

    <p><strong>Title:</strong> <?= htmlspecialchars($bug['title']) ?></p>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($bug['description'])) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($bug['status']) ?></p>

    <a href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
