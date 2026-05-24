<?php
require_once 'config.php';

$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($studentId <= 0) {
    header("Location: dashboard.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
    $stmt->execute(['id' => $studentId]);
    $student = $stmt->fetch();
    
    if (!$student) {
        $errorNotFound = true;
    } else {
        $errorNotFound = false;
    }
} catch (PDOException $e) {
    die("Database Error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant View - startocode.com</title>
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
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="register.php" class="nav-link">Portal</a></li>
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="register.php" class="btn-nav-started">Get Started</a></li>
            </ul>
        </div>
    </header>

    <!-- Success/Error Toast Notifications Container -->
    <div class="toast-container" id="toast-notification"></div>

    <main class="main-container">
        
        <?php if ($errorNotFound): ?>
            <div class="panel text-center">
                <div class="no-data">
                    <div class="no-data-icon">⚠️</div>
                    <h3>Applicant Not Found</h3>
                    <p>The student record does not exist.</p>
                    <a href="dashboard.php" class="btn btn-primary mt-4">&larr; Back to Dashboard</a>
                </div>
            </div>
        <?php else: ?>
            
            <div class="mb-4">
                <a href="dashboard.php" class="btn btn-secondary">
                    &larr; Back to Dashboard
                </a>
            </div>

            <!-- Profile Redesign matching screenshot side-by-side layout -->
            <div class="profile-split-container">
                
                <!-- LEFT SIDEBAR PROFILE CARD -->
                <div class="profile-sidebar-card">
                    <div class="profile-avatar-wrapper">
                        <img src="<?php echo e($student['profile_image']); ?>" alt="Student Photo" class="profile-avatar">
                    </div>
                    <div class="profile-name-tag">
                        <h2>
                            <?php 
                                $fullName = $student['first_name'] . ' ' . $student['last_name'];
                                echo e($fullName);
                            ?>
                        </h2>
                        <!-- Status label below name -->
                        <span class="profile-status-label <?php echo $student['admission_status'] === 'Admitted' ? 'profile-status-label-admitted' : 'profile-status-label-undecided'; ?>" id="card-status-badge">
                            <?php echo e(strtolower($student['admission_status'])); ?>
                        </span>
                    </div>
                </div>

                <!-- RIGHT INFORMATION CARD -->
                <div class="profile-info-card">
                    
                    <!-- 1. Personal Information Section -->
                    <div class="section-sub-header-bar">
                        Personal Information
                    </div>
                    <div class="profile-details-grid">
                        <div class="profile-label">Email:</div>
                        <div class="profile-value"><?php echo e($student['email']); ?></div>

                        <div class="profile-label">Gender:</div>
                        <div class="profile-value" id="profile-gender-val"><?php echo e(strtolower($student['gender'])); ?></div>

                        <div class="profile-label">Phone Number:</div>
                        <div class="profile-value"><?php echo e($student['phone_number']); ?></div>

                        <div class="profile-label">Date Of Birth:</div>
                        <div class="profile-value"><?php echo e($student['date_of_birth']); ?></div>

                        <div class="profile-label">Address:</div>
                        <div class="profile-value"><?php echo e($student['address']); ?></div>
                    </div>

                    <!-- 2. Other Information Section -->
                    <div class="section-sub-header-bar">
                        Other Information
                    </div>
                    <div class="profile-details-grid">
                        <div class="profile-label">State Of Origin:</div>
                        <div class="profile-value"><?php echo e($student['state_of_origin']); ?></div>

                        <div class="profile-label">Local Govt:</div>
                        <div class="profile-value"><?php echo e($student['lga']); ?></div>
                    </div>

                    <!-- 3. Academics Related Information Section -->
                    <div class="section-sub-header-bar">
                        Academics Related Information
                    </div>
                    <div class="profile-details-grid">
                        <div class="profile-label">Next Of Kin:</div>
                        <div class="profile-value"><?php echo e($student['next_of_kin']); ?></div>

                        <div class="profile-label">Jamb Score:</div>
                        <div class="profile-value" style="font-weight: bold;"><?php echo intval($student['jamb_score']); ?></div>

                        <div class="profile-label">Status:</div>
                        <div class="profile-value">
                            <div class="admission-status-toggle-container">
                                <span class="badge <?php echo $student['admission_status'] === 'Admitted' ? 'badge-admitted' : 'badge-undecided'; ?>" id="detail-status-badge">
                                    <?php echo e(strtolower($student['admission_status'])); ?>
                                </span>
                                <label class="switch">
                                    <input type="checkbox" id="admission-toggle" data-id="<?php echo $student['id']; ?>" <?php echo $student['admission_status'] === 'Admitted' ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <p>All rights reserved @startocode 2026</p>
        <p>Developer: Isaac Ofori &nbsp;|&nbsp; +233594844398</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('admission-toggle');
            const cardBadge = document.getElementById('card-status-badge');
            const detailBadge = document.getElementById('detail-status-badge');
            
            if (toggle) {
                toggle.addEventListener('change', function() {
                    const isChecked = this.checked;
                    const studentId = this.getAttribute('data-id');
                    const newStatus = isChecked ? 'Admitted' : 'Undecided';
                    
                    // Disable inputs during request
                    this.disabled = true;
                    
                    fetch('update_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id: studentId,
                            status: newStatus
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response failure.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const lowercaseStatus = newStatus.toLowerCase();
                            
                            // 1. Update left sidebar label
                            cardBadge.textContent = lowercaseStatus;
                            if (newStatus === 'Admitted') {
                                cardBadge.className = 'profile-status-label profile-status-label-admitted';
                            } else {
                                cardBadge.className = 'profile-status-label profile-status-label-undecided';
                            }

                            // 2. Update right details badge
                            detailBadge.textContent = lowercaseStatus;
                            if (newStatus === 'Admitted') {
                                detailBadge.className = 'badge badge-admitted';
                            } else {
                                detailBadge.className = 'badge badge-undecided';
                            }

                            showToast(data.message, 'success');
                        } else {
                            showToast(data.message, 'danger');
                            this.checked = !isChecked; // Revert checkbox
                        }
                    })
                    .catch(error => {
                        console.error('AJAX Error:', error);
                        showToast('Failed to update status on server.', 'danger');
                        this.checked = !isChecked; // Revert checkbox
                    })
                    .finally(() => {
                        this.disabled = false;
                    });
                });
            }

            function showToast(message, type) {
                const container = document.getElementById('toast-notification');
                container.innerHTML = ''; // clear previous
                
                const toast = document.createElement('div');
                toast.className = `alert-message alert-${type}`;
                
                const icon = type === 'success' ? '✨' : '⚠️';
                toast.innerHTML = `<span>${icon}</span><div>${message}</div>`;
                
                container.appendChild(toast);
                
                setTimeout(() => {
                    toast.classList.add('fade-out');
                    setTimeout(() => toast.remove(), 400);
                }, 4000);
            }
        });
    </script>
</body>
</html>
