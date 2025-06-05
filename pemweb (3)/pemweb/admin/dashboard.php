<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_technicians = $conn->query("SELECT COUNT(*) FROM technicians")->fetch_row()[0];
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetch_row()[0];

// Get recent orders
$recent_orders = $conn->query("
    SELECT o.id, u.name as user_name, t.user_id as tech_id, 
           it.name as item_type, o.status, o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN technicians t ON o.technician_id = t.id
    LEFT JOIN item_types it ON o.item_type_id = it.id
    ORDER BY o.created_at DESC
    LIMIT 5
");

// Get top technicians
$top_technicians = $conn->query("
    SELECT u.name, t.rate, COUNT(o.id) as order_count, 
           AVG(f.rating) as avg_rating
    FROM technicians t
    JOIN users u ON t.user_id = u.id
    LEFT JOIN orders o ON t.id = o.technician_id
    LEFT JOIN feedback f ON o.id = f.order_id
    GROUP BY t.id
    ORDER BY avg_rating DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .stat-card {
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include 'sidebar.php'; ?>
            </div>
            <div class="col-md-9">
                <h2>Dashboard Overview</h2>
                
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h2><?= $total_users ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Technicians</h5>
                                <h2><?= $total_technicians ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Orders</h5>
                                <h2><?= $total_orders ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-dark">
                            <div class="card-body">
                                <h5 class="card-title">Pending Orders</h5>
                                <h2><?= $pending_orders ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Recent Orders</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>User</th>
                                                <th>Service</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($order = $recent_orders->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $order['id'] ?></td>
                                                <td><?= htmlspecialchars($order['user_name']) ?></td>
                                                <td><?= htmlspecialchars($order['item_type']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= 
                                                        $order['status'] == 'completed' ? 'success' : 
                                                        ($order['status'] == 'processing' ? 'info' : 'warning')
                                                    ?>">
                                                        <?= ucfirst($order['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Top Technicians</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Rate</th>
                                                <th>Orders</th>
                                                <th>Rating</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($tech = $top_technicians->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($tech['name']) ?></td>
                                                <td>$<?= number_format($tech['rate'], 2) ?>/hr</td>
                                                <td><?= $tech['order_count'] ?></td>
                                                <td>
                                                    <?= $tech['avg_rating'] ? number_format($tech['avg_rating'], 1) : 'N/A' ?>
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>