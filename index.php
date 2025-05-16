<?php session_start();
require 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RepairPlus - Quick Repair</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="login-status">
    <?php
      if (isset($_SESSION['username'])) {
        echo "<p>Welcome, " . htmlspecialchars($_SESSION['username']) . " | <a href='logout.php'>Logout</a></p>";
      } else {
        echo "<p><a href='login.php'>Login</a></p>";
      }
    ?>
  </div>

  <section class="hero">
    <div class="hero-text">
      <h1>Welcome to RepairTech quick repair</h1>
      <p>Using original manufactured parts and components, we are able to restore your cell phones or iDevice to like-new condition.</p>
      <button class="btn-primary">BUY NOW</button>
      <button class="btn-secondary">CONTACT US</button>
    </div>
    <img src="https://i.ibb.co/Y3tJP70/phone-repair.jpg" alt="Phone Repair" />
  </section>

  <section class="services">
    <div class="service-box">
      <img src="https://i.ibb.co/1m1Xq2n/smartphone-repair.jpg" alt="Smartphone Repair">
      <h3>SMART PHONE REPAIR</h3>
      <p>We specialise in Phone repairs for Apple iPhones, iPad, iPod, Samsung, Galaxy, Sony, HTC, Nexus, Motorola, Blackberry & Tablets.</p>
    </div>
    <div class="service-box" style="background:#d9f2ff">
      <img src="https://i.ibb.co/hZRhF6s/tablet-repair.jpg" alt="Tablet Repair">
      <h3>TABLETS & IPAD REPAIR</h3>
      <p>If you are facing any problem with your Tablets / Ipads, Kindly pls go through the mentioned catagories as per requirement.</p>
    </div>
    <div class="service-box">
      <img src="https://i.ibb.co/TTvBP6y/desktop-repair.jpg" alt="Desktop Repair">
      <h3>DESKTOP & MAC REPAIR</h3>
      <p>We specialist in providing On-Site Computer Desktop Repair Service and Network Support for all sized business. On-Site Computer.</p>
    </div>
  </section>
</body>
</html>
