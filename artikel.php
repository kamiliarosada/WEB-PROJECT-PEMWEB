<?php
session_start();
require_once '../db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get the selected category filter
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : null;

// Build the SQL query with optional category filter
$sql = "
    SELECT a.*, u.name as author_name, sc.name as service_category_name
    FROM articles a
    JOIN users u ON a.author_id = u.id
    LEFT JOIN service_categories sc ON a.service_category_id = sc.id
    WHERE a.is_published = 1
";

if ($category_filter) {
    $sql .= " AND a.service_category_id = $category_filter";
}

$sql .= " ORDER BY a.created_at DESC";

// Execute the query
$articles = $conn->query($sql);

// Get all service categories for the filter dropdown
$categories = $conn->query("SELECT * FROM service_categories");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Artikel Perbaikan Elektronik - Reparo</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
      color: #333;
      line-height: 1.6;
      min-height: 100vh;
    }
    
    /* Header Navigation */
    .nav-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 15px 20px;
      background: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 1.5rem;
      font-weight: 700;
      color: #2563eb;
      text-decoration: none;
    }
    
    .logo i {
      font-size: 1.8rem;
    }
    
    .nav-links {
      display: flex;
      gap: 25px;
    }
    
    .nav-links a {
      text-decoration: none;
      color: #1e293b;
      font-weight: 500;
      transition: color 0.3s ease;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .nav-links a:hover {
      color: #2563eb;
    }
    
    .nav-links a.active {
      color: #2563eb;
      font-weight: 600;
    }
    
    .user-actions {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .user-actions a {
      text-decoration: none;
      color: #1e293b;
      transition: color 0.3s ease;
    }
    
    .user-actions a:hover {
      color: #2563eb;
    }
    
    .user-profile {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      position: relative;
    }
    
    .user-img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      background: #2563eb;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
    }
    
    .dropdown-menu {
      position: absolute;
      top: 45px;
      right: 0;
      background: white;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      padding: 10px 0;
      min-width: 180px;
      display: none;
      z-index: 100;
    }
    
    .dropdown-menu a {
      display: block;
      padding: 8px 15px;
      color: #334155;
      text-decoration: none;
      transition: background 0.3s;
    }
    
    .dropdown-menu a:hover {
      background: #f1f5f9;
    }
    
    .user-profile:hover .dropdown-menu {
      display: block;
    }
    
    .container {
      max-width: 1200px;
      margin: 30px auto;
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }
    
    header {
      background: linear-gradient(90deg, #2563eb 0%, #1d4ed8 100%);
      color: white;
      padding: 30px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    header::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
      transform: rotate(30deg);
    }
    
    h1 {
      font-size: 2.8rem;
      margin-bottom: 10px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
      position: relative;
    }
    
    .subtitle {
      font-size: 1.2rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto;
      position: relative;
    }
    
    .filters {
      display: flex;
      justify-content: center;
      gap: 15px;
      padding: 20px;
      background: #f1f5f9;
      border-bottom: 1px solid #e2e8f0;
    }
    
    .filter-btn {
      padding: 8px 20px;
      background: white;
      border: 1px solid #cbd5e1;
      border-radius: 30px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 0.95rem;
      text-decoration: none;
      color: #333;
    }
    
    .filter-btn:hover, .filter-btn.active {
      background: #2563eb;
      color: white;
      border-color: #2563eb;
    }
    
    .article-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      padding: 30px;
    }
    
    .article-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    
    .article-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }
    
    .card-image {
      height: 200px;
      overflow: hidden;
      position: relative;
    }
    
    .card-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    
    .article-card:hover .card-image img {
      transform: scale(1.05);
    }
    
    .card-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background: rgba(37, 99, 235, 0.9);
      color: white;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }
    
    .card-content {
      padding: 25px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }
    
    .article-card h3 {
      color: #1e40af;
      margin-bottom: 15px;
      font-size: 1.5rem;
    }
    
    .article-meta {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 15px;
      font-size: 0.85rem;
      color: #64748b;
    }
    
    .article-meta span {
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .article-card p {
      color: #4b5563;
      margin-bottom: 20px;
      flex-grow: 1;
    }
    
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 12px 25px;
      background: linear-gradient(90deg, #2563eb 0%, #1d4ed8 100%);
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
    }
    
    .btn:hover {
      background: linear-gradient(90deg, #1d4ed8 0%, #1e40af 100%);
      box-shadow: 0 6px 15px rgba(37, 99, 235, 0.4);
      transform: translateY(-2px);
    }
    
    .btn i {
      margin-right: 8px;
    }
    
    .features {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
      padding: 40px 30px;
      background: #f1f5f9;
      border-top: 1px solid #e2e8f0;
      border-bottom: 1px solid #e2e8f0;
    }
    
    .feature {
      text-align: center;
      padding: 20px;
      border-radius: 10px;
      background: white;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      transition: transform 0.3s ease;
      flex: 1;
      min-width: 200px;
      max-width: 250px;
    }
    
    .feature:hover {
      transform: translateY(-5px);
    }
    
    .feature i {
      font-size: 2.5rem;
      color: #2563eb;
      margin-bottom: 15px;
    }
    
    .feature h4 {
      color: #1e293b;
      margin-bottom: 10px;
    }
    
    .feature p {
      color: #64748b;
      font-size: 0.95rem;
    }
    
    footer {
      text-align: center;
      padding: 30px;
      background: #1e293b;
      color: #cbd5e1;
    }
    
    .footer-content {
      max-width: 600px;
      margin: 0 auto;
    }
    
    .social-icons {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin: 20px 0;
    }
    
    .social-icons a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      background: #334155;
      border-radius: 50%;
      color: white;
      transition: all 0.3s ease;
    }
    
    .social-icons a:hover {
      background: #2563eb;
      transform: translateY(-3px);
    }
    
    .copyright {
      margin-top: 20px;
      font-size: 0.9rem;
      opacity: 0.8;
    }
    
    @media (max-width: 768px) {
      .article-list {
        grid-template-columns: 1fr;
        padding: 20px;
      }
      
      h1 {
        font-size: 2.2rem;
      }
      
      .features {
        flex-direction: column;
        align-items: center;
      }
      
      .feature {
        max-width: 100%;
      }
      
      .nav-links {
        display: none;
      }
    }
    
    @media (max-width: 480px) {
      header {
        padding: 20px;
      }
      
      h1 {
        font-size: 1.8rem;
      }
      
      .subtitle {
        font-size: 1rem;
      }
      
      .filters {
        flex-wrap: wrap;
      }
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <div class="nav-container">
    <a href="../index.php" class="logo">
      <i class="fas fa-tools"></i>
      <span>ReparoTech</span>
    </a>
    
    <div class="nav-links">
      <a href="../index.php"><i class="fas fa-home"></i> Beranda</a>
      <a href="service.php"><i class="fas fa-headset"></i> Layanan</a>
      <a href="pesanan.php"><i class="fas fa-clipboard-list"></i> Pesanan</a>
      <a href="artikel.php" class="active"><i class="fas fa-newspaper"></i> Artikel</a>
    </div>
    
    <div class="user-actions">
      <div class="user-profile">
        <div class="user-img"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <div class="dropdown-menu">
          <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
          <a href="riwayat-pesanan.php"><i class="fas fa-history"></i> Riwayat</a>
          <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <header>
      <h1><i class="fas fa-tools"></i> Panduan Perbaikan Elektronik</h1>
      <p class="subtitle">Temukan solusi praktis untuk memperbaiki perangkat elektronik rumah tangga Anda dengan panduan langkah demi langkah dari para ahli</p>
    </header>
    
    <div class="filters">
      <a href="artikel.php" class="filter-btn <?= !$category_filter ? 'active' : '' ?>">Semua</a>
      <?php while($category = $categories->fetch_assoc()): ?>
        <a href="artikel.php?category=<?= $category['id'] ?>" class="filter-btn <?= $category_filter == $category['id'] ? 'active' : '' ?>">
          <?= htmlspecialchars($category['name']) ?>
        </a>
      <?php endwhile; ?>
    </div>
    
    <div class="article-list">
      <?php if ($articles->num_rows > 0): ?>
        <?php while($article = $articles->fetch_assoc()): ?>
        <div class="article-card">
          <div class="card-image">
            <img src="<?= $article['image_url'] ? '../' . $article['image_url'] : 'https://via.placeholder.com/600x400?text=Reparo+Artikel' ?>" alt="<?= htmlspecialchars($article['title']) ?>">
            <span class="card-badge"><?= $article['service_category_name'] ?: 'Umum' ?></span>
          </div>
          <div class="card-content">
            <h3><i class="fas fa-<?= 
              strpos($article['title'], 'Kulkas') !== false ? 'snowflake' : 
              (strpos($article['title'], 'AC') !== false ? 'wind' : 
              (strpos($article['title'], 'Mesin Cuci') !== false ? 'soap' : 
              (strpos($article['title'], 'TV') !== false ? 'tv' : 
              (strpos($article['title'], 'Laptop') !== false ? 'laptop' : 'tools')))) ?>"></i> <?= htmlspecialchars($article['title']) ?></h3>
            
            <div class="article-meta">
              <span><i class="fas fa-user"></i> <?= htmlspecialchars($article['author_name']) ?></span>
              <span><i class="fas fa-calendar"></i> <?= date('d M Y', strtotime($article['created_at'])) ?></span>
            </div>
            
            <p><?= htmlspecialchars(substr($article['content'], 0, 150)) ?>...</p>
            <a href="detail_artikel.php?id=<?= $article['id'] ?>" class="btn"><i class="fas fa-book-open"></i> Baca Selengkapnya</a>
          </div>
        </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
          <h3>Tidak ada artikel yang ditemukan</h3>
          <p>Silakan coba dengan kategori yang berbeda</p>
        </div>
      <?php endif; ?>
    </div>
    
    <footer>
      <div class="footer-content">
        <h3>ReparoTech</h3>
        <p>Solusi perbaikan elektronik terbaik dengan kualitas dan kepercayaan sebagai prioritas utama kami.</p>
        
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-youtube"></i></a>
        </div>
        
        <p class="copyright">© 2023 Reparo Perbaikan Elektronik. Hak Cipta Dilindungi.</p>
      </div>
    </footer>
  </div>

  <script>
    // Animation for article cards
    setTimeout(() => {
      document.querySelectorAll('.article-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
      });
      
      setTimeout(() => {
        document.querySelectorAll('.article-card').forEach((card, i) => {
          setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, i * 100);
        });
      }, 100);
    }, 500);
  </script>
</body>
</html>