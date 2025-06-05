<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);

// Check if technician exists
$technician = $conn->query("
    SELECT t.*, u.name as user_name 
    FROM technicians t
    JOIN users u ON t.user_id = u.id
    WHERE t.id = $id
")->fetch_assoc();

if (!$technician) {
    $_SESSION['error'] = "Technician not found";
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Perform the deletion
    try {
        $conn->begin_transaction();
        
        // Delete related records first if needed (example: appointments, services)
        // $conn->query("DELETE FROM appointments WHERE technician_id = $id");
        
        // Then delete the technician
        $conn->query("DELETE FROM technicians WHERE id = $id");
        
        $conn->commit();
        
        $_SESSION['success'] = "Technician deleted successfully";
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error deleting technician: " . $e->getMessage();
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Technician</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include '../sidebar.php'; ?>
            </div>
            <div class="col-md-9">
                <h2>Delete Technician</h2>
                
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <h4 class="alert-heading">Warning!</h4>
                            <p>You are about to delete the following technician. This action cannot be undone.</p>
                        </div>
                        
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Technician Name</th>
                                <td><?= htmlspecialchars($technician['user_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Specialization</th>
                                <td><?= htmlspecialchars($technician['specialization']) ?></td>
                            </tr>
                            <tr>
                                <th>Experience</th>
                                <td><?= $technician['experience_years'] ?> years</td>
                            </tr>
                        </table>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="confirm" class="form-label">
                                    Type "DELETE" to confirm
                                </label>
                                <input type="text" class="form-control" id="confirm" name="confirm" 
                                       placeholder="Type DELETE here" required>
                            </div>
                            
                            <button type="submit" class="btn btn-danger" id="deleteBtn" disabled>
                                <i class="bi bi-trash"></i> Confirm Delete
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable delete button only when user types "DELETE"
        document.getElementById('confirm').addEventListener('input', function(e) {
            document.getElementById('deleteBtn').disabled = 
                e.target.value.toUpperCase() !== 'DELETE';
        });
    </script>
</body>
</html>