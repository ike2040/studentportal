<?php
require_once 'config.php';

$successMessage = '';
$errorMessage = '';

// Pre-populate values in case of error
$form_data = [
    'first_name' => '',
    'middle_name' => '',
    'last_name' => '',
    'email' => '',
    'date_of_birth' => '',
    'gender' => '',
    'phone_number' => '',
    'address' => '',
    'state_of_origin' => '',
    'lga' => '',
    'next_of_kin' => '',
    'jamb_score' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input data
    foreach ($form_data as $key => $val) {
        if (isset($_POST[$key])) {
            $form_data[$key] = trim($_POST[$key]);
        }
    }
    
    // Server-side validation
    $required_fields = ['first_name', 'last_name', 'email', 'date_of_birth', 'gender', 'phone_number', 'address', 'state_of_origin', 'lga', 'next_of_kin', 'jamb_score'];
    $isValid = true;
    
    foreach ($required_fields as $field) {
        if (empty($form_data[$field])) {
            $isValid = false;
            $errorMessage = "Please fill in all required fields.";
            break;
        }
    }
    
    // Email format check
    if ($isValid && !filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $isValid = false;
        $errorMessage = "Invalid email format.";
    }
    
    // JAMB score check
    if ($isValid) {
        $jamb = intval($form_data['jamb_score']);
        if ($jamb < 0 || $jamb > 400) {
            $isValid = false;
            $errorMessage = "JAMB Score must be between 0 and 400.";
        }
    }
    
    // Profile image upload check
    $imageUploaded = false;
    $targetFile = '';
    
    if ($isValid) {
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_image']['tmp_name'];
            $fileName = $_FILES['profile_image']['name'];
            $fileSize = $_FILES['profile_image']['size'];
            
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                // Limit file size to 5MB
                if ($fileSize <= 5 * 1024 * 1024) {
                    $newFileName = uniqid('img_', true) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/uploads/';
                    $targetFile = 'uploads/' . $newFileName;
                    $destPath = $uploadFileDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $imageUploaded = true;
                    } else {
                        $isValid = false;
                        $errorMessage = "There was an error moving the uploaded profile image.";
                    }
                } else {
                    $isValid = false;
                    $errorMessage = "Uploaded image exceeds the maximum limit of 5MB.";
                }
            } else {
                $isValid = false;
                $errorMessage = "Invalid image file type. Only JPG, JPEG, PNG, and WEBP are allowed.";
            }
        } else {
            $isValid = false;
            $errorMessage = "Profile image is required.";
        }
    }
    
    // Check if email already exists
    if ($isValid) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE email = :email");
            $stmt->execute(['email' => $form_data['email']]);
            if ($stmt->fetchColumn() > 0) {
                $isValid = false;
                $errorMessage = "A student with this email address is already registered.";
                if ($imageUploaded && file_exists($targetFile)) {
                    unlink($targetFile);
                }
            }
        } catch (PDOException $e) {
            $isValid = false;
            $errorMessage = "Database verification error: " . $e->getMessage();
        }
    }
    
    // Database Insertion
    if ($isValid) {
        try {
            $sql = "INSERT INTO students (first_name, middle_name, last_name, email, date_of_birth, gender, phone_number, address, state_of_origin, lga, next_of_kin, jamb_score, profile_image, admission_status) 
                    VALUES (:first_name, :middle_name, :last_name, :email, :date_of_birth, :gender, :phone_number, :address, :state_of_origin, :lga, :next_of_kin, :jamb_score, :profile_image, 'Undecided')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'first_name' => $form_data['first_name'],
                'middle_name' => $form_data['middle_name'] !== '' ? $form_data['middle_name'] : null,
                'last_name' => $form_data['last_name'],
                'email' => $form_data['email'],
                'date_of_birth' => $form_data['date_of_birth'],
                'gender' => $form_data['gender'],
                'phone_number' => $form_data['phone_number'],
                'address' => $form_data['address'],
                'state_of_origin' => $form_data['state_of_origin'],
                'lga' => $form_data['lga'],
                'next_of_kin' => $form_data['next_of_kin'],
                'jamb_score' => intval($form_data['jamb_score']),
                'profile_image' => $targetFile
            ]);
            
            $successMessage = "Student registration completed successfully!";
            
            // Clear form data on success
            foreach ($form_data as $key => $val) {
                $form_data[$key] = '';
            }
        } catch (PDOException $e) {
            if ($imageUploaded && file_exists($targetFile)) {
                unlink($targetFile);
            }
            $errorMessage = "Registration failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal Form - startocode.com</title>
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
                <li><a href="register.php" class="nav-link active">Portal</a></li>
                <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="register.php" class="btn-nav-started">Get Started</a></li>
            </ul>
        </div>
    </header>

    <!-- Success/Error Toast Notifications -->
    <?php if (!empty($successMessage) || !empty($errorMessage)): ?>
        <div class="toast-container" id="toast-notification">
            <?php if (!empty($successMessage)): ?>
                <div class="alert-message alert-success">
                    <span>✨</span>
                    <div><?php echo e($successMessage); ?></div>
                </div>
            <?php endif; ?>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert-message alert-danger">
                    <span>⚠️</span>
                    <div><?php echo e($errorMessage); ?></div>
                </div>
            <?php endif; ?>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-notification');
                if (toast) {
                    toast.classList.add('fade-out');
                    setTimeout(() => toast.remove(), 400);
                }
            }, 5000);
        </script>
    <?php endif; ?>

    <main class="main-container">
        
        <!-- Banner Title matching screenshot style -->
        <div class="page-title-banner">
            Student Portal Form
        </div>
        
        <!-- Required instruction subtext -->
        <div class="form-subtext" style="color: #ef4444; font-size: 0.85rem; text-align: center; margin-top: 0.25rem; margin-bottom: 1.5rem; font-weight: 600;">
            Please fill in all required information
        </div>

        <form action="register.php" method="POST" enctype="multipart/form-data">
            
            <!-- SECTION 1: Personal Information -->
            <div class="section-header-bar" style="background-color: #4b5563; color: #ffffff; padding: 0.65rem 1.25rem; font-weight: 700; font-size: 0.95rem; text-transform: capitalize; border-radius: 4px 4px 0 0; margin-top: 1.5rem; margin-bottom: 0;">
                Personal Information
            </div>
            
            <div class="panel" style="background-color: #e0f2fe; border: 1px solid #bae6fd; border-top: none; border-radius: 0 0 4px 4px; padding: 1.5rem 2rem; margin-top: 0; margin-bottom: 1.5rem; box-shadow: none;">
                <div class="form-grid">
                    
                    <!-- Profile Image Upload -->
                    <div class="form-group form-full-width">
                        <label class="form-label">Upload Image:</label>
                        <div class="image-input-container">
                            <img id="preview-img" src="" alt="Profile Preview" class="image-mini-preview" style="display: none;">
                            <input type="file" name="profile_image" id="profile_image" class="form-input" accept="image/*" style="background-color: #ffffff;" required>
                        </div>
                    </div>

                    <!-- First Name & Middle Name -->
                    <div class="form-group">
                        <label for="first_name" class="form-label">FirstName</label>
                        <input type="text" id="first_name" name="first_name" class="form-input" placeholder="Enter Firstname" style="background-color: #ffffff;" value="<?php echo e($form_data['first_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="middle_name" class="form-label">MidCoffee</label>
                        <input type="text" id="middle_name" name="middle_name" class="form-input" placeholder="Enter Middlename" style="background-color: #ffffff;" value="<?php echo e($form_data['middle_name']); ?>">
                    </div>

                    <!-- Last Name & Email -->
                    <div class="form-group">
                        <label for="last_name" class="form-label">LastName</label>
                        <input type="text" id="last_name" name="last_name" class="form-input" placeholder="Enter Lastname" style="background-color: #ffffff;" value="<?php echo e($form_data['last_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="Enter Email Address" style="background-color: #ffffff;" value="<?php echo e($form_data['email']); ?>" required>
                    </div>

                    <!-- Date Of Birth & Gender (Radio options) -->
                    <div class="form-group">
                        <label for="date_of_birth" class="form-label">Date Of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="form-input" style="background-color: #ffffff;" value="<?php echo e($form_data['date_of_birth']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <div class="radio-group" style="padding-top: 0.4rem;">
                            <label class="radio-label">
                                <input type="radio" name="gender" value="Male" <?php echo $form_data['gender'] === 'Male' ? 'checked' : ''; ?> required> Male
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="gender" value="Female" <?php echo $form_data['gender'] === 'Female' ? 'checked' : ''; ?> required> Female
                            </label>
                        </div>
                    </div>

                    <!-- Phone Number & Address -->
                    <div class="form-group">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number" class="form-input" placeholder="Enter PhoneNumber" style="background-color: #ffffff;" value="<?php echo e($form_data['phone_number']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" id="address" name="address" class="form-input" placeholder="Enter Address" style="background-color: #ffffff;" value="<?php echo e($form_data['address']); ?>" required>
                    </div>

                    <!-- State of Origin & Local Government -->
                    <div class="form-group">
                        <label for="state_of_origin" class="form-label">State Of Origin</label>
                        <select id="state_of_origin" name="state_of_origin" class="form-select" style="background-color: #ffffff;" required>
                            <option value="" disabled selected>Select State</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lga" class="form-label">Local Government</label>
                        <select id="lga" name="lga" class="form-select" style="background-color: #ffffff;" disabled required>
                            <option value="" disabled selected>Select Local Government</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Academics Related Information -->
            <div class="section-header-bar" style="background-color: #4b5563; color: #ffffff; padding: 0.65rem 1.25rem; font-weight: 700; font-size: 0.95rem; text-transform: capitalize; border-radius: 4px 4px 0 0; margin-top: 1.5rem; margin-bottom: 0;">
                Academics Related Information
            </div>

            <div class="panel" style="background-color: #e0f2fe; border: 1px solid #bae6fd; border-top: none; border-radius: 0 0 4px 4px; padding: 1.5rem 2rem; margin-top: 0; margin-bottom: 1.5rem; box-shadow: none;">
                <div class="form-grid">
                    <!-- Next Of Kin & JAMB Score -->
                    <div class="form-group">
                        <label for="next_of_kin" class="form-label">Next Of Kin</label>
                        <input type="text" id="next_of_kin" name="next_of_kin" class="form-input" placeholder="Enter The Name Of NextOfKin" style="background-color: #ffffff;" value="<?php echo e($form_data['next_of_kin']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="jamb_score" class="form-label">Jamb Score</label>
                        <input type="number" id="jamb_score" name="jamb_score" class="form-input" placeholder="Enter Jamb Score" style="background-color: #ffffff;" min="0" max="400" value="<?php echo e($form_data['jamb_score']); ?>" required>
                    </div>
                </div>

                <!-- Submit Button Wrapper matching screenshot -->
                <div class="form-submit-wrapper" style="background-color: transparent; text-align: center; margin-top: 1rem; padding: 0; border-radius: 0;">
                    <button type="submit" class="btn btn-primary" style="background-color: #4b5563; color: #ffffff; padding: 0.5rem 3rem; font-size: 0.95rem; border-radius: 4px; border: none; font-weight: bold; cursor: pointer;">
                        Submit
                    </button>
                </div>
            </div>

        </form>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <p>All rights reserved @startocode 2026</p>
        <p>Developer: Isaac Ofori &nbsp;|&nbsp; +233594844398</p>
    </footer>

    <script src="js/register.js"></script>
</body>
</html>
