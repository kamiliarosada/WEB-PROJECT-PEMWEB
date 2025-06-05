<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RepairPlus - Quick Repair</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <?php include 'users/header.php'; ?>

  <div class="login-status">
    <?php
    
      if (!empty($_SESSION['username'])) {
          $user = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
          echo "<p>Welcome, {$user} | <a href='logout.php'>Logout</a></p>";
      } else {
          echo "<p><a href='login.php'>Login</a></p>";
      }
    ?>
  </div>

  <section class="hero">
    <div class="hero-text">
      <h1>Welcome to ReparoTech Quick Repair</h1>
      <p>Using original manufactured parts and components, we are able to restore your cell phones or iDevices to like-new condition.</p>
      <button class="btn-primary">BUY NOW</button>
      <button class="btn-secondary">CONTACT US</button>
    </div>
    <img src="https://i.ibb.co/Y3tJP70/phone-repair.jpg" alt="Phone Repair" />
  </section>

  <section class="services">
    <div class="service-box">
      <img src="https://i.ibb.co/1m1Xq2n/smartphone-repair.jpg" alt="Smartphone Repair">
      <h3>SMART PHONE REPAIR</h3>
      <p>We specialise in phone repairs for Apple iPhones, iPads, iPods, Samsung, Sony, HTC, Nexus, Motorola, Blackberry & Tablets.</p>
    </div>
    <div class="service-box" style="background:#d9f2ff">
      <img src="https://i.ibb.co/hZRhF6s/tablet-repair.jpg" alt="Tablet Repair">
      <h3>TABLETS & IPAD REPAIR</h3>
      <p>If you are facing any problem with your tablets or iPads, please go through the categories as per your requirement.</p>
    </div>
    <div class="service-box">
      <img src="https://i.ibb.co/TTvBP6y/desktop-repair.jpg" alt="Desktop Repair">
      <h3>DESKTOP & MAC REPAIR</h3>
      <p>We specialise in providing on-site computer and desktop repair services and network support for businesses of all sizes.</p>
    </div>
  </section>
</body>
</html>
