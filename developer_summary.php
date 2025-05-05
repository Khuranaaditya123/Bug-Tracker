<?php
include("config.php"); // Include DB connection
// if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] != "admin") {
//     header("Location: index.php");
//     exit;
// }
// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['ajax']) && $_GET['ajax'] == 'true') {
    header('Content-Type: application/json');

    $devQuery = "SELECT id, name FROM users WHERE role = 'developer'";
    $devResult = $conn->query($devQuery);

    $developerStats = [];

    if ($devResult && $devResult->num_rows > 0) {
        while ($dev = $devResult->fetch_assoc()) {
            $devId = $dev['id'];

            // Count Fixed bugs
            $fixed = $conn->query("SELECT COUNT(*) as count FROM bugs WHERE assigned_to = $devId AND status = 'Fixed'");
            $fixedCount = $fixed->fetch_assoc()['count'];

            // Count Closed bugs
            $closed = $conn->query("SELECT COUNT(*) as count FROM bugs WHERE assigned_to = $devId AND status = 'Closed'");
            $closedCount = $closed->fetch_assoc()['count'];

            // Count Open/In Progress bugs
            $open = $conn->query("SELECT COUNT(*) as count FROM bugs WHERE assigned_to = $devId AND status IN ('Open', 'In Progress')");
            $openCount = $open->fetch_assoc()['count'];

            // Store result
            $developerStats[] = [
                'developer_id' => $devId,
                'developer_name' => $dev['name'],
                'fixed_count' => $fixedCount,
                'closed_count' => $closedCount,
                'open_in_progress_count' => $openCount
            ];
        }
    }

    echo json_encode($developerStats);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Developer Bug Summary</title>
    <style>
        body {
            background-color: #0f0f0f;
            color: #e0e0e0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #00c4ff;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #333;
            text-align: center;
        }
        th {
            background-color: #1e1e1e;
            color: #00ffff;
        }
        tr:hover {
            background-color: #1a1a1a;
        }
        .button {
            background-color: #00c4ff;
            color: black;
            padding: 6px 10px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #00a0cc;
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
            text-decoration: none;
        }
    </style>
</head>
<body>

<h2>Developer Bug Summary (Live)</h2>

<table id="developerTable">
    <thead>
        <tr>
            <th>Developer Name</th>
            <th>Fixed</th>
            <th>Closed</th>
            <th>Open/In Progress</th>
            <th>View</th>
        </tr>
    </thead>
    <tbody>
        <!-- Filled via JavaScript -->
    </tbody>
    
</table>
<a href="dashboard.php">← Back to Dashboard</a>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function updateDeveloperStats() {
    $.ajax({
        url: 'developer_summary.php',
        method: 'GET',
        data: { ajax: true },
        success: function(response) {
            let rows = '';
            response.forEach(dev => {
                rows += `
                    <tr>
                        <td>${dev.developer_name}</td>
                        <td>${dev.fixed_count}</td>
                        <td>${dev.closed_count}</td>
                        <td>${dev.open_in_progress_count}</td>
                        <td><a class="button" href="developer_dashboard.php?developer_id=${dev.developer_id}">View</a></td>
                    </tr>
                `;
            });
            $('#developerTable tbody').html(rows);
        },
        error: function(xhr, status, error) {
            console.error("Error fetching developer stats:", error);
        }
    });
}

$(document).ready(() => {
    updateDeveloperStats();
    setInterval(updateDeveloperStats, 10000); // refresh every 10 seconds
});
</script>

</body>
</html>
