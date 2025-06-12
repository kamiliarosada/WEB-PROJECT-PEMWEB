<?php
session_start();
require_once '../db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Dapatkan ID artikel dari URL
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data artikel dari database
$stmt = $conn->prepare("
    SELECT a.*, u.name AS author_name, sc.name AS service_category_name 
    FROM articles a
    JOIN users u ON a.author_id = u.id
    LEFT JOIN service_categories sc ON a.service_category_id = sc.id
    WHERE a.id = ?
");
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

// Jika artikel tidak ditemukan, redirect atau tampilkan pesan error
if (!$article) {
    die("Artikel tidak ditemukan");
}

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    $rating = intval($_POST['rating']);
    $user_id = $_SESSION['user_id'];
    
    // Check if user already rated this article
    $check_stmt = $conn->prepare("SELECT id FROM article_ratings WHERE article_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $article_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing rating
        $update_stmt = $conn->prepare("UPDATE article_ratings SET rating = ? WHERE article_id = ? AND user_id = ?");
        $update_stmt->bind_param("iii", $rating, $article_id, $user_id);
        $update_stmt->execute();
    } else {
        // Insert new rating
        $insert_stmt = $conn->prepare("INSERT INTO article_ratings (article_id, user_id, rating) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $article_id, $user_id, $rating);
        $insert_stmt->execute();
    }
    
    $_SESSION['rating_submitted'] = true;
    header("Location: detail_artikel.php?id=$article_id");
    exit;
}

// Get average rating
$rating_stmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as rating_count FROM article_ratings WHERE article_id = ?");
$rating_stmt->bind_param("i", $article_id);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result();
$rating_data = $rating_result->fetch_assoc();
$average_rating = round($rating_data['avg_rating'], 1);
$rating_count = $rating_data['rating_count'];

// Check if user already rated this article
$user_rated = false;
$user_rating = 0;
if (isset($_SESSION['user_id'])) {
    $user_rating_stmt = $conn->prepare("SELECT rating FROM article_ratings WHERE article_id = ? AND user_id = ?");
    $user_rating_stmt->bind_param("ii", $article_id, $_SESSION['user_id']);
    $user_rating_stmt->execute();
    $user_rating_result = $user_rating_stmt->get_result();
    if ($user_rating_result->num_rows > 0) {
        $user_rated = true;
        $user_rating = $user_rating_result->fetch_assoc()['rating'];
    }
}

// Prepare variables for display
$content = $article['content'];
$title = $article['title'];
$image_url = $article['image_url'] ? '../' . $article['image_url'] : 'https://via.placeholder.com/800x400?text=Reparo+Artikel';
$created_at = date('d F Y', strtotime($article['created_at']));
$author = $article['author_name'];
$category = $article['service_category_name'] ?: 'Umum';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Reparo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        
        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
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
            max-width: 900px;
            margin: 30px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(90deg, #0066cc 0%, #0099cc 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }
        
        h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }
        
        .article-meta {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 15px;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .details-section {
            padding: 30px;
        }
        
        .image-container {
            width: 100%;
            max-height: 400px;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .image-container img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s ease;
        }
        
        .image-container img:hover {
            transform: scale(1.03);
        }
        
        h4 {
            color: #0066cc;
            margin-bottom: 20px;
            font-size: 1.8rem;
            border-bottom: 2px solid #0099cc;
            padding-bottom: 8px;
        }
        
        ol {
            margin-left: 20px;
            margin-bottom: 25px;
        }
        
        li {
            margin-bottom: 20px;
        }
        
        strong {
            color: #0066cc;
        }
        
        ul {
            margin-top: 10px;
            margin-left: 20px;
        }
        
        ul li {
            margin-bottom: 8px;
            list-style-type: disc;
        }
        
        .rating-container {
            background: #f0f9ff;
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
            border: 1px solid #b3e0ff;
        }
        
        .rating-title {
            font-size: 1.4rem;
            color: #0066cc;
            margin-bottom: 15px;
        }
        
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            margin: 15px 0;
        }
        
        .star-rating input {
            display: none;
        }
        
        .star-rating label {
            color: #ccc;
            font-size: 32px;
            padding: 0 3px;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .star-rating label:before {
            content: "★";
        }
        
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffcc00;
        }
        
        .submit-btn {
            background: linear-gradient(90deg, #0066cc 0%, #0099cc 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 50px;
            cursor: pointer;
            margin-top: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 102, 204, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 102, 204, 0.4);
            background: linear-gradient(90deg, #0055aa 0%, #0088bb 100%);
        }
        
        .submit-btn:active {
            transform: translateY(1px);
        }
        
        .thank-you {
            background: #e6ffed;
            border-radius: 10px;
            padding: 20px;
            margin-top: 25px;
            color: #009933;
            font-weight: bold;
            display: none;
            animation: fadeIn 0.5s ease-in-out;
            border: 1px solid #99ff99;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .rating-result {
            font-size: 1.2rem;
            margin-top: 15px;
            font-weight: bold;
            color: #0066cc;
            text-align: center;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            background: #0066cc;
            color: white;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .details-section {
                padding: 20px;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            h4 {
                font-size: 1.5rem;
            }
            
            .star-rating label {
                font-size: 28px;
            }
            
            .article-meta {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="nav-container">
        <a href="#" class="logo">
            <i class="fas fa-tools"></i>
            <span>Reparo</span>
        </a>
        
        <div class="user-actions">
            <a href="#"><i class="fas fa-bell"></i></a>
            <div class="user-profile">
                <div class="user-img"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></div>
                <span><?= $_SESSION['username'] ?></span>
                <div class="dropdown-menu">
                    <a href="profile.php"><i class="fas fa-user"></i> Profil Saya</a>
                    <a href="#"><i class="fas fa-history"></i> Riwayat</a>
                    <a href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <header>
            <h1><?= htmlspecialchars($title) ?></h1>
            <div class="article-meta">
                <span><i class="fas fa-user"></i> <?= htmlspecialchars($author) ?></span>
                <span><i class="fas fa-calendar"></i> <?= $created_at ?></span>
                <span><i class="fas fa-tag"></i> <?= htmlspecialchars($category) ?></span>
            </div>
        </header>
        
        <div class="details-section">
            <a href="artikel.php" class="back-btn" style="display: inline-flex; align-items: center; gap: 5px; margin-bottom: 20px; text-decoration: none; color: #0066cc; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Artikel
            </a>
            
            <div class="image-container">
                <img src="<?= $image_url ?>" alt="<?= htmlspecialchars($title) ?>">
            </div>
            
            <?= $content ?>
            
            <div class="rating-container">
                <div class="rating-title">Berikan Rating untuk Panduan Ini</div>
                <p>Bagaimana pengalaman Anda dengan panduan ini?</p>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5">
                    <label for="star5" title="Sangat memuaskan"></label>
                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4" title="Memuaskan"></label>
                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3" title="Cukup baik"></label>
                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2" title="Kurang memuaskan"></label>
                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1" title="Tidak memuaskan"></label>
                </div>
                <button class="submit-btn" id="submit-rating">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 2L11 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Kirim Rating
                </button>
                <div class="thank-you" id="thank-you-message">
                    Terima kasih atas rating Anda! Umpan balik Anda sangat berharga untuk meningkatkan kualitas panduan kami.
                </div>
                <div class="rating-result" id="rating-result"></div>
            </div>
        </div>

        <footer>
            <p>© <?= date('Y') ?> Panduan Perbaikan Elektronik. Hak Cipta Dilindungi.</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const submitBtn = document.getElementById('submit-rating');
            const thankYouMessage = document.getElementById('thank-you-message');
            const ratingResult = document.getElementById('rating-result');
            const ratingInputs = document.querySelectorAll('input[name="rating"]');
            const stars = Array.from(document.querySelectorAll('.star-rating label')).reverse();
            
            // Star hover animation
            stars.forEach((star, index) => {
                star.addEventListener('mouseover', function() {
                    const hoverValue = 5 - index;
                    highlightStars(hoverValue);
                });
                
                star.addEventListener('mouseout', function() {
                    const selected = document.querySelector('input[name="rating"]:checked');
                    if (selected) {
                        highlightStars(parseInt(selected.value));
                    } else {
                        resetStars();
                    }
                });
            });
            
            submitBtn.addEventListener('click', function() {
                let selectedRating = 0;
                
                // Find selected rating
                for (const input of ratingInputs) {
                    if (input.checked) {
                        selectedRating = parseInt(input.value);
                        break;
                    }
                }
                
                if (selectedRating === 0) {
                    alert('Silakan pilih rating terlebih dahulu!');
                    return;
                }
                
                // Show thank you message
                thankYouMessage.style.display = 'block';
                
                // Show rating result
                ratingResult.textContent = `Anda memberikan rating: ${selectedRating}/5 bintang`;
                
                // Button animation
                submitBtn.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12L11 14L15 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="9" stroke="white" stroke-width="2"/>
                    </svg>
                    Rating Terkirim
                `;
                submitBtn.style.background = 'linear-gradient(90deg, #009933 0%, #00cc66 100%)';
                submitBtn.disabled = true;
                
                // In a real app, you would send the rating to the server here
                console.log(`Rating: ${selectedRating} stars for article ID <?= $article_id ?>`);
            });
            
            function highlightStars(value) {
                resetStars();
                for (let i = 1; i <= value; i++) {
                    const star = document.querySelector(`label[for="star${i}"]`);
                    if (star) {
                        star.style.color = '#ffcc00';
                    }
                }
            }
            
            function resetStars() {
                const stars = document.querySelectorAll('.star-rating label');
                stars.forEach(star => {
                    star.style.color = '#ccc';
                });
            }
        });
    </script>
</body>
</html>