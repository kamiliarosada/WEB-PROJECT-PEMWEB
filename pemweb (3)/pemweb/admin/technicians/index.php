<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}
// Handle actions
if (isset($_GET['action'])) {
    $id = intval($_GET['id']);
    
    switch ($_GET['action']) {
        case 'verify':
            $conn->query("UPDATE technicians SET is_verified = 1 WHERE id = $id");
            break;
        case 'unverify':
            $conn->query("UPDATE technicians SET is_verified = 0 WHERE id = $id");
            break;
        case 'delete':
            $conn->query("DELETE FROM technicians WHERE id = $id");
            break;
    }
    
    header('Location: index.php');
    exit;
}

// Get all technicians
$technicians = $conn->query("
    SELECT t.*, u.name, u.email, u.phone 
    FROM technicians t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Management</title>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Technician Management</h2>
                    <a href="add.php" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Add Technician
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Specialization</th>
                                        <th>Rate</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($tech = $technicians->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $tech['id'] ?></td>
                                        <td>
                                            <?= htmlspecialchars($tech['name']) ?>
                                            <br><small class="text-muted"><?= $tech['email'] ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($tech['specialization']) ?></td>
                                        <td>$<?= number_format($tech['rate'], 2) ?>/hr</td>
                                        <td>
                                            <?php if($tech['is_verified']): ?>
                                                <span class="badge bg-success">Verified</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Unverified</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="edit.php?id=<?= $tech['id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                <?php if($tech['is_verified']): ?>
                                                    <a href="?action=unverify&id=<?= $tech['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-x-circle"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="?action=verify&id=<?= $tech['id'] ?>" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="?action=delete&id=<?= $tech['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
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