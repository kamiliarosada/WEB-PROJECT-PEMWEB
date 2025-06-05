<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialization = $conn->real_escape_string($_POST['specialization']);
    $experience = intval($_POST['experience']);
    $certification = $conn->real_escape_string($_POST['certification']);
    $rate = floatval($_POST['rate']);
    
    $conn->query("UPDATE technicians SET 
                 specialization = '$specialization', 
                 experience_years = $experience, 
                 certification = '$certification', 
                 rate = $rate 
                 WHERE id = $id");
    
    header('Location: index.php');
    exit;
}

$technician = $conn->query("
    SELECT t.*, u.name as user_name 
    FROM technicians t
    JOIN users u ON t.user_id = u.id
    WHERE t.id = $id
")->fetch_assoc();

if (!$technician) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Technician</title>
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
                <h2>Edit Technician: <?= htmlspecialchars($technician['user_name']) ?></h2>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Specialization</label>
                                <input type="text" name="specialization" class="form-control" 
                                       value="<?= htmlspecialchars($technician['specialization']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Experience (years)</label>
                                <input type="number" name="experience" class="form-control" 
                                       value="<?= $technician['experience_years'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Certification</label>
                                <input type="text" name="certification" class="form-control" 
                                       value="<?= htmlspecialchars($technician['certification']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hourly Rate ($)</label>
                                <input type="number" step="0.01" name="rate" class="form-control" 
                                       value="<?= $technician['rate'] ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="index.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>