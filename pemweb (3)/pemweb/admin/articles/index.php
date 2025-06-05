<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// Handle article deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM articles WHERE id = $id");
    header('Location: index.php');
    exit;
}

$articles = $conn->query("
    SELECT a.*, u.name as author_name, c.name as category_name, sc.name as service_category_name
    FROM articles a
    JOIN users u ON a.author_id = u.id
    LEFT JOIN categories c ON a.category_id = c.id
    LEFT JOIN service_categories sc ON a.service_category_id = sc.id
    ORDER BY a.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Articles</title>
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
                    <h2>Article Management</h2>
                    <a href="add.php" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Add Article
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Categories</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($article = $articles->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $article['id'] ?></td>
                                        <td><?= htmlspecialchars($article['title']) ?></td>
                                        <td><?= htmlspecialchars($article['author_name']) ?></td>
                                        <td>
                                            <?= $article['category_name'] ? htmlspecialchars($article['category_name']) : '' ?>
                                            <?= $article['service_category_name'] ? '<br>' . htmlspecialchars($article['service_category_name']) : '' ?>
                                        </td>
                                        <td>
                                            <?php if($article['is_published']): ?>
                                                <span class="badge bg-success">Published</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Draft</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($article['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="edit.php?id=<?= $article['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="?delete=<?= $article['id'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
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