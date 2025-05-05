<?php
include("config.php");
// if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] != "admin") {
//     header("Location: index.php");
//     exit;
// }
if (!isset($_GET['developer_id'])) {
    echo "Developer ID is missing.";
    exit;
}

$developer_id = intval($_GET['developer_id']);

// Get developer name
$nameQuery = $conn->query("SELECT name FROM users WHERE id = $developer_id");
if ($nameQuery->num_rows === 0) {
    echo "Developer not found.";
    exit;
}
$developer_name = $nameQuery->fetch_assoc()['name'];

// Bug counts by status
$statusCounts = [
    'Open/In Progress' => 0,
    'Fixed' => 0,
    'Closed' => 0
];
$statusQuery = "SELECT status, COUNT(*) as count FROM bugs WHERE assigned_to = $developer_id GROUP BY status";
$statusResult = $conn->query($statusQuery);
while ($row = $statusResult->fetch_assoc()) {
    if (in_array($row['status'], ['Open', 'In Progress'])) {
        $statusCounts['Open/In Progress'] += $row['count'];
    } else {
        $statusCounts[$row['status']] = $row['count'];
    }
}

// Bug counts by severity
$severityData = [];
$severityQuery = "SELECT severity, COUNT(*) as count FROM bugs WHERE assigned_to = $developer_id GROUP BY severity";
$severityResult = $conn->query($severityQuery);
while ($row = $severityResult->fetch_assoc()) {
    $severityData[$row['severity']] = $row['count'];
}

?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $developer_name ?>'s Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #00c4ff;
        }
        .charts {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 40px;
        }
        .chart-box {
            width: 45%;
            background: #1e1e1e;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.1);
            margin-bottom: 30px;
        }
        canvas {
            width: 100%;
            height: auto;
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
        @media(max-width: 768px) {
            .chart-box {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<h2>Bug Report for <?= htmlspecialchars($developer_name) ?></h2>

<div class="charts">
    <div class="chart-box">
        <canvas id="statusChart"></canvas>
    </div>
    <div class="chart-box">
        <canvas id="severityChart"></canvas>
    </div>
</div>
<a href="dashboard.php">← Back to Dashboard</a>
<script>
const statusData = {
    labels: ['Open/In Progress', 'Fixed', 'Closed'],
    datasets: [{
        label: 'Bug Status',
        data: [
            <?= $statusCounts['Open/In Progress'] ?? 0 ?>,
            <?= $statusCounts['Fixed'] ?? 0 ?>,
            <?= $statusCounts['Closed'] ?? 0 ?>
        ],
        backgroundColor: ['#e74c3c', '#2ecc71', '#3498db'],
        borderColor: '#121212',
        borderWidth: 2
    }]
};

new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: statusData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { color: '#fff' },
                position: 'bottom'
            }
        }
    }
});

const severityData = {
    labels: <?= json_encode(array_keys($severityData)) ?>,
    datasets: [{
        label: 'Bug Severity',
        data: <?= json_encode(array_values($severityData)) ?>,
        backgroundColor: ['#e74c3c', '#f39c12', '#3498db', '#2ecc71'],
        borderWidth: 1
    }]
};

new Chart(document.getElementById('severityChart'), {
    type: 'bar',
    data: severityData,
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: '#fff' }
            },
            x: {
                ticks: { color: '#fff' }
            }
        },
        plugins: {
            legend: {
                labels: { color: '#fff' }
            }
        }
    }
});
</script>

</body>
</html>
