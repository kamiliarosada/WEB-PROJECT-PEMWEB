<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$new_users = $conn->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_row()[0];
$total_technicians = $conn->query("SELECT COUNT(*) FROM technicians")->fetch_row()[0];
$verified_technicians = $conn->query("SELECT COUNT(*) FROM technicians WHERE is_verified = 1")->fetch_row()[0];
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$completed_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'")->fetch_row()[0];
$revenue = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetch_row()[0];

// Get user growth data for chart
$user_growth = $conn->query("
    SELECT DATE(created_at) as date, COUNT(*) as count 
    FROM users 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date
");

// Get order status distribution
$order_status = $conn->query("
    SELECT status, COUNT(*) as count 
    FROM orders 
    GROUP BY status
");

// Get top service categories
$top_categories = $conn->query("
    SELECT sc.name, COUNT(o.id) as order_count
    FROM orders o
    JOIN item_types it ON o.item_type_id = it.id
    JOIN service_categories sc ON it.service_category_id = sc.id
    GROUP BY sc.id
    ORDER BY order_count DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include '../sidebar.php'; ?>
            </div>
            <div class="col-md-9">
                <h2>System Statistics</h2>
                
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h2><?= $total_users ?></h2>
                                <small><?= $new_users ?> new this week</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Technicians</h5>
                                <h2><?= $total_technicians ?></h2>
                                <small><?= $verified_technicians ?> verified</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Orders</h5>
                                <h2><?= $total_orders ?></h2>
                                <small><?= $completed_orders ?> completed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <h5 class="card-title">Total Revenue</h5>
                                <h2>$<?= number_format($revenue, 2) ?></h2>
                                <small>from completed orders</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>User Growth (Last 30 Days)</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="userGrowthChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Order Status Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="orderStatusChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Top Service Categories</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="topCategoriesChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        const userGrowthChart = new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php 
                    $user_growth_data = [];
                    while($row = $user_growth->fetch_assoc()) {
                        echo "'" . date('M j', strtotime($row['date'])) . "',";
                        $user_growth_data[] = $row['count'];
                    }
                    ?>
                ],
                datasets: [{
                    label: 'New Users',
                    data: [<?= implode(',', $user_growth_data) ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Order Status Chart
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        const orderStatusChart = new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php 
                    $status_labels = [];
                    $status_data = [];
                    $status_colors = [];
                    
                    while($row = $order_status->fetch_assoc()) {
                        echo "'" . ucfirst($row['status']) . "',";
                        $status_data[] = $row['count'];
                        
                        // Assign colors based on status
                        switch($row['status']) {
                            case 'pending': $status_colors[] = "'#ffc107'"; break;
                            case 'processing': $status_colors[] = "'#17a2b8'"; break;
                            case 'completed': $status_colors[] = "'#28a745'"; break;
                            case 'cancelled': $status_colors[] = "'#dc3545'"; break;
                            default: $status_colors[] = "'#6c757d'";
                        }
                    }
                    ?>
                ],
                datasets: [{
                    data: [<?= implode(',', $status_data) ?>],
                    backgroundColor: [<?= implode(',', $status_colors) ?>],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        
        // Top Categories Chart
        const topCategoriesCtx = document.getElementById('topCategoriesChart').getContext('2d');
        const topCategoriesChart = new Chart(topCategoriesCtx, {
            type: 'bar',
            data: {
                labels: [
                    <?php 
                    $category_labels = [];
                    $category_data = [];
                    
                    while($row = $top_categories->fetch_assoc()) {
                        echo "'" . htmlspecialchars($row['name']) . "',";
                        $category_data[] = $row['order_count'];
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Number of Orders',
                    data: [<?= implode(',', $category_data) ?>],
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>