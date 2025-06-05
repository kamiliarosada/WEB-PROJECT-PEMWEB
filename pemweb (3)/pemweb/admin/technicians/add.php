<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $specialization = $conn->real_escape_string($_POST['specialization']);
    $experience = intval($_POST['experience']);
    $certification = $conn->real_escape_string($_POST['certification']);
    $rate = floatval($_POST['rate']);
    
    $conn->query("INSERT INTO technicians (user_id, specialization, experience_years, certification, rate) 
                 VALUES ($user_id, '$specialization', $experience, '$certification', $rate)");
    
    header('Location: index.php');
    exit;
}

// Get all users who aren't already technicians
$users = $conn->query("
    SELECT u.id, u.name 
    FROM users u 
    LEFT JOIN technicians t ON u.id = t.user_id 
    WHERE t.id IS NULL AND u.role = 'user'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Technician</title>
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
                <h2>Add New Technician</h2>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">User</label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">Select User</option>
                                    <?php while($user = $users->fetch_assoc()): ?>
                                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Specialization</label>
                                <input type="text" name="specialization" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Experience (years)</label>
                                <input type="number" name="experience" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Certification</label>
                                <input type="text" name="certification" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hourly Rate ($)</label>
                                <input type="number" step="0.01" name="rate" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Technician</button>
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