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
    $where = "assigned_to = $user_id";
} elseif ($role == "tester") {
    $where = "reported_by = $user_id";
}

function getCount($conn, $where, $status = "") {
    $query = "SELECT COUNT(*) as count FROM bugs";
    if ($where) {
        $query .= " WHERE $where";
        if ($status) {
            $query .= " AND $status";
        }
    } elseif ($status) {
        $query .= " WHERE $status";
    }
    $result = $conn->query($query);
    return $result ? $result->fetch_assoc()["count"] : 0;
}

$total = getCount($conn, $where);
$open = getCount($conn, $where, "status IN ('Open', 'In Progress')");
$fixed = getCount($conn, $where, "status = 'Fixed'");
$closed = getCount($conn, $where, "status = 'Closed'");

// 📈 Trend chart data
$trendQuery = "SELECT DATE(created_at) as date, COUNT(*) as count FROM bugs";
if ($where) {
    $trendQuery .= " WHERE $where";
}
$trendQuery .= " GROUP BY DATE(created_at) ORDER BY date ASC";

$trendResult = $conn->query($trendQuery);
$dates = [];
$bugCounts = [];
while ($row = $trendResult->fetch_assoc()) {
    $dates[] = '"' . $row['date'] . '"';
    $bugCounts[] = $row['count'];
}
$dates = implode(",", $dates);
$bugCounts = implode(",", $bugCounts);

// 📊 Severity-wise bug counts from DB
$severityQuery = "SELECT severity, COUNT(*) as count FROM bugs";
if ($where) {
    $severityQuery .= " WHERE $where";
}
$severityQuery .= " GROUP BY severity";

$severityResult = $conn->query($severityQuery);

$severityLabels = [];
$severityCounts = [];

while ($row = $severityResult->fetch_assoc()) {
    $severityLabels[] = '"' . $row['severity'] . '"';
    $severityCounts[] = $row['count'];
}

$severityLabels = implode(",", $severityLabels);
$severityCounts = implode(",", $severityCounts);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= ucfirst($role) ?> Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 30px;
            color: #00c4ff;
        }

        .stats {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: linear-gradient(145deg, #1a1a1a, #333);
            padding: 25px;
            border-radius: 15px;
            min-width: 220px;
            flex: 1 1 220px;
            max-width: 240px;
            text-align: center;
            border: 1px solid #444;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.1);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            color: #ffffff;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.2);
        }

        .card strong {
            font-size: 1.8rem;
            color: #00c4ff;
        }

        .chart-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: stretch;
    gap: 20px;
    margin-bottom: 40px;
}

.chart-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 40px;
}

.chart-container {
    flex: 1 1 48%;
    max-width: 48%;
    background-color: #1a1a1a;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0, 255, 255, 0.08);
    display: flex;
    justify-content: center;
    align-items: center;
    aspect-ratio: 1 / 1; /* Ensures square aspect */
}

.chart-container canvas {
    width: 100% !important;
    height: auto !important;
    max-height: 100% !important;
    aspect-ratio: 1 / 1;
    object-fit: contain;
}

@media (max-width: 768px) {
    .chart-container {
        max-width: 100%;
        flex: 1 1 100%;
        aspect-ratio: unset;
    }

    .chart-row {
        flex-direction: column;
    }
}


        .full-width-chart {
            max-width: 90%;
            margin: 0 auto 40px;
        }

        canvas {
            width: 100% !important;
            height: auto !important;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #00c4ff;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
        }

        /* a:hover {
            color: #00eaff;
        } */
        .btn {
    display: inline-block;
    padding: 12px 20px;
    margin: 5px 10px;
    font-size: 1rem;
    font-weight: bold;
    color: #fff;
    background-color: #00c4ff;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.3s ease, transform 0.2s ease;
    box-shadow: 0 0 10px rgba(0, 196, 255, 0.4);
}

.btn:hover {
    background-color: #00eaff;
    transform: translateY(-2px);
}

        @media (max-width: 768px) {
            .chart-container {
                max-width: 100%;
            }

            .chart-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<h2>Welcome, <?= ucfirst($role) ?>!</h2>
<div style="text-align:center; margin-bottom: 30px;">
    <?php if ($role == 'admin'): ?>
        <a href="assign_bug.php" class="btn">➕ Assign Bug</a>
        <a href="developer_summary.php" class="btn">📊 Developer Summary</a>
        <a href="index.php" class="btn">LogOut</a>
    <?php elseif ($role == 'developer'): ?>
        <a href="view_bugs.php" class="btn">🐞 View Bugs</a>
        <a href="index.php" class="btn">LogOut</a>
    <?php elseif ($role == 'tester'): ?>
        <a href="report_bug.php" class="btn">📝 Report Bug</a>
        <a href="index.php" class="btn">LogOut</a>
    <?php endif; ?>
</div>


<div class="stats">
    <div class="card">Total Bugs<br><strong><?= $total ?></strong></div>
    <div class="card">Open / In Progress<br><strong><?= $open ?></strong></div>
    <div class="card">Fixed<br><strong><?= $fixed ?></strong></div>
    <div class="card">Closed<br><strong><?= $closed ?></strong></div>
</div>

<!-- Pie and Line Charts Side by Side -->
<div class="chart-row">
    <div class="chart-container">
        <canvas id="statusChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="bugTrendChart"></canvas>
    </div>
</div>

<!-- Additional Chart Below -->
<div class="full-width-chart">
    <canvas id="severityChart"></canvas>
</div>

<a href="all_bugs.php">📋 View All Bugs</a>


<script>
// Pie Chart - Bug Status
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: ['Open/In Progress', 'Fixed', 'Closed'],
        datasets: [{
            data: [<?= $open ?>, <?= $fixed ?>, <?= $closed ?>],
            backgroundColor: ['#e74c3c', '#2ecc71', '#3498db'],
            borderColor: '#121212',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { color: '#fff', font: { size: 14 } },
                position: 'bottom'
            }
        }
    }
});

// Line Chart - Bug Trend
const trendCtx = document.getElementById('bugTrendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: [<?= $dates ?>],
        datasets: [{
            label: 'Bugs Reported',
            data: [<?= $bugCounts ?>],
            borderColor: '#00c4ff',
            backgroundColor: 'rgba(0,196,255,0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, ticks: { color: '#fff' } },
            x: { ticks: { color: '#fff' } }
        },
        plugins: {
            legend: { labels: { color: '#fff' } }
        }
    }
});

// Bar Chart - Severity
const ctx = document.getElementById('severityChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?= $severityLabels ?>],
            datasets: [{
                label: 'Bug Severity',
                data: [<?= $severityCounts ?>],
                backgroundColor: [
                    '#e74c3c', // Critical
                    '#f39c12', // Severe
                    '#3498db', // Moderate
                    '#2ecc71'  // Minor
                ],
                borderWidth: 1
            }]
        },
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
