<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// Handle order actions
if (isset($_GET['action']) {
    $id = intval($_GET['id']);
    
    switch ($_GET['action']) {
        case 'verify':
            $conn->query("UPDATE orders SET is_verified = 1 WHERE id = $id");
            break;
        case 'unverify':
            $conn->query("UPDATE orders SET is_verified = 0 WHERE id = $id");
            break;
        case 'delete':
            $conn->query("DELETE FROM orders WHERE id = $id");
            break;
        case 'update_status':
            $status = $conn->real_escape_string($_POST['status']);
            $conn->query("UPDATE orders SET status = '$status' WHERE id = $id");
            break;
    }
    
    header('Location: index.php');
    exit;
}

// Get all orders with user and technician details
$orders = $conn->query("
    SELECT o.*, u.name as user_name, t.user_id as tech_id, 
           it.name as item_type, sc.name as service_category
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN technicians t ON o.technician_id = t.id
    LEFT JOIN item_types it ON o.item_type_id = it.id
    LEFT JOIN service_categories sc ON it.service_category_id = sc.id
    ORDER BY o.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include '../sidebar.php'; ?>
            </div>
            <div class="col-md-9">
                <h2>Order Management</h2>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>User</th>
                                        <th>Service</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Verified</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $order['id'] ?></td>
                                        <td><?= htmlspecialchars($order['user_name']) ?></td>
                                        <td><?= htmlspecialchars($order['item_type']) ?></td>
                                        <td><?= $order['service_category'] ? htmlspecialchars($order['service_category']) : 'N/A' ?></td>
                                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                        <td>
                                            <form method="POST" action="?action=update_status&id=<?= $order['id'] ?>" class="d-inline">
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                                    <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <?php if($order['is_verified']): ?>
                                                <span class="badge bg-success">Verified</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Unverified</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if($order['is_verified']): ?>
                                                    <a href="?action=unverify&id=<?= $order['id'] ?>" class="btn btn-sm btn-warning" title="Unverify">
                                                        <i class="bi bi-x-circle"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="?action=verify&id=<?= $order['id'] ?>" class="btn btn-sm btn-success" title="Verify">
                                                        <i class="bi bi-check-circle"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="?action=delete&id=<?= $order['id'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>