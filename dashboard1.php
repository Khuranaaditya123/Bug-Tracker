<?php
include("config.php");

if (!isset($_GET['ajax'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request"]);
    exit;
}

header('Content-Type: application/json');

$query = "
    SELECT 
        u.id AS developer_id,
        u.name AS developer_name,
        SUM(CASE WHEN b.status = 'Fixed' THEN 1 ELSE 0 END) AS fixed_count,
        SUM(CASE WHEN b.status = 'Closed' THEN 1 ELSE 0 END) AS closed_count,
        SUM(CASE WHEN b.status IN ('Open', 'In Progress') THEN 1 ELSE 0 END) AS open_in_progress_count
    FROM users u
    LEFT JOIN bugs b ON u.id = b.assigned_to
    WHERE u.role = 'developer'
    GROUP BY u.id
";

$result = $conn->query($query);
$developers = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $developers[] = $row;
    }
}

echo json_encode($developers);
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
        <!-- Populated via JavaScript -->
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function updateDeveloperStats() {
    $.ajax({
        url: 'developer_summary.php',
        method: 'GET',
        dataType: 'json',
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
    setInterval(updateDeveloperStats, 10000); // Refresh every 10 seconds
});
</script>

</body>
</html>
