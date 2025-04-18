<?php
session_start();
include 'db_connect.php';

// Check if user is an admin
// You can customize this based on your admin system

// Fetch total failed attempts and actual attackers
$total_failed = $conn->query("SELECT COUNT(*) as count FROM login_attempts WHERE success = 0")->fetch_assoc()['count'];
$true_positive = $conn->query("SELECT COUNT(*) as count FROM login_attempts WHERE success = 0 AND is_hacker = 1")->fetch_assoc()['count'];
$false_positive = $conn->query("SELECT COUNT(*) as count FROM login_attempts WHERE success = 0 AND is_hacker = 0")->fetch_assoc()['count'];
$false_negative = $conn->query("SELECT COUNT(*) as count FROM login_attempts WHERE success = 1 AND is_hacker = 1")->fetch_assoc()['count'];

// Avoid divide by zero
$precision = ($true_positive + $false_positive) > 0 ? $true_positive / ($true_positive + $false_positive) : 0;
$recall    = ($true_positive + $false_negative) > 0 ? $true_positive / ($true_positive + $false_negative) : 0;
$f1_score  = ($precision + $recall) > 0 ? 2 * (($precision * $recall) / ($precision + $recall)) : 0;

// Fetch login attempts for table
$attempts = $conn->query("SELECT * FROM login_attempts ORDER BY timestamp DESC LIMIT 50");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Evaluation Metrics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 40px;
        }

        h1 {
            text-align: center;
        }

        .metrics {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-bottom: 40px;
        }

        .metric-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #ccc;
            text-align: center;
            width: 200px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 0px 10px #ccc;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: white;
        }

        .chart-container {
            width: 600px;
            margin: 0 auto 50px auto;
        }
    </style>
</head>

<body>

    <h1>Login System Evaluation Metrics</h1>

    <div class="metrics">
        <div class="metric-box">
            <h3>Precision</h3>
            <p><?= round($precision * 100, 2) ?>%</p>
        </div>
        <div class="metric-box">
            <h3>Recall</h3>
            <p><?= round($recall * 100, 2) ?>%</p>
        </div>
        <div class="metric-box">
            <h3>F1 Score</h3>
            <p><?= round($f1_score * 100, 2) ?>%</p>
        </div>
    </div>

    <div class="chart-container">
        <canvas id="attemptChart"></canvas>
    </div>

    <table>
        <tr>
            <th>Email</th>
            <th>IP Address</th>
            <th>Timestamp</th>
            <th>Success</th>
            <th>Is Hacker</th>
        </tr>
        <?php while ($row = $attempts->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['ip_address'] ?></td>
                <td><?= $row['timestamp'] ?></td>
                <td><?= $row['success'] ? '✅' : '❌' ?></td>
                <td><?= $row['is_hacker'] ? '🛑' : '👤' ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <script>
        const ctx = document.getElementById('attemptChart').getContext('2d');
        const attemptChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['True Positives', 'False Positives', 'False Negatives'],
                datasets: [{
                    label: 'Login Attempt Evaluation',
                    data: [<?= $true_positive ?>, <?= $false_positive ?>, <?= $false_negative ?>],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 206, 86, 0.7)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>