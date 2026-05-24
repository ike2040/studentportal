<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Main Portal - startocode.com</title>
    <meta name="description" content="Ustacky Student Portal registration, join us today for your online courses.">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Header Navigation matching the screenshot dark style -->
    <header class="main-header">
        <div class="nav-container">
            <a href="index.php" class="logo">
                Student Portal
            </a>
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link active">Home</a></li>
                <li><a href="register.php" class="nav-link">Portal</a></li>
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="register.php" class="btn-nav-started">Get Started</a></li>
            </ul>
        </div>
    </header>

    <!-- Main Hero Section matching Screen 1 -->
    <div class="hero-wrapper" style="background: #ffffff; min-height: calc(100vh - 120px); display: flex; align-items: center; justify-content: center; padding: 2rem 0;">
        <main class="main-container" style="margin: 0 auto; padding: 0 2rem; width: 100%;">
            <div class="landing-grid">
                
                <!-- LEFT: Hero content block -->
                <div class="landing-content">
                    <h1 style="color: #1f1c50; font-size: 3rem; line-height: 1.25; margin-bottom: 0.75rem; font-weight: 800;">
                        Student Main Portal
                    </h1>
                    
                    <p style="color: #d97706; font-size: 1.05rem; font-weight: 700; margin-bottom: 2.25rem; max-width: 480px; line-height: 1.6;">
                        Ustacky Student Portal registration, join us today for your online courses
                    </p>
                    
                    <div class="hero-cta-row">
                        <a href="register.php" class="btn btn-primary" style="background-color: #1e1145; padding: 0.65rem 2.25rem; font-size: 0.95rem; border-radius: 4px; font-weight: 700; text-transform: none;">
                            Get Started
                        </a>
                    </div>
                </div>

                <!-- RIGHT: Classroom illustration -->
                <div class="tablet-container" style="background: none; box-shadow: none; padding: 0; display: flex; justify-content: center; align-items: center;">
                    <img
                        src="images/students_studying.png"
                        alt="Classroom Instruction Illustration"
                        style="width: 100%; max-width: 460px; height: auto; display: block; border-radius: 0; box-shadow: none; border: none;"
                    >
                </div>

            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <p>All rights reserved @startocode 2026</p>
        <p>Developer: Isaac Ofori &nbsp;|&nbsp; +233594844398</p>
    </footer>

</body>
</html>
