<?php
require_once 'config.php';

// Prepare filters
$whereClauses = [];
$params = [];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$gender = isset($_GET['gender']) ? trim($_GET['gender']) : '';
$minJamb = isset($_GET['min_jamb']) && $_GET['min_jamb'] !== '' ? intval($_GET['min_jamb']) : '';

if ($search !== '') {
    $whereClauses[] = "(first_name LIKE :search OR middle_name LIKE :search OR last_name LIKE :search)";
    $params['search'] = '%' . $search . '%';
}

if ($status !== '') {
    if (in_array($status, ['Admitted', 'Undecided'])) {
        $whereClauses[] = "admission_status = :status";
        $params['status'] = $status;
    }
}

if ($gender !== '') {
    if (in_array($gender, ['Male', 'Female'])) {
        $whereClauses[] = "gender = :gender";
        $params['gender'] = $gender;
    }
}

if ($minJamb !== '') {
    $whereClauses[] = "jamb_score >= :min_jamb";
    $params['min_jamb'] = $minJamb;
}

// Fetch students
try {
    $sql = "SELECT * FROM students";
    if (count($whereClauses) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll();
    
    // Fetch count stats for dashboard cards
    $totalCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $admittedCount = $pdo->query("SELECT COUNT(*) FROM students WHERE admission_status = 'Admitted'")->fetchColumn();
    $undecidedCount = $pdo->query("SELECT COUNT(*) FROM students WHERE admission_status = 'Undecided'")->fetchColumn();
    
} catch (PDOException $e) {
    die("Database Query Failed: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ScholarGrad Student Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Standout Metrics dashboard row */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        .stat-card {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 1.25rem;
            border-left: 4px solid var(--accent-primary);
        }
        .stat-icon {
            font-size: 1.75rem;
            width: 3rem;
            height: 3rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-details h4 {
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.15rem;
        }
        .stat-details p {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1;
        }
    </style>
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
                <li><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
                <li><a href="register.php" class="btn-nav-started">Get Started</a></li>
            </ul>
        </div>
    </header>

    <main class="main-container">
        
        <!-- Banner Title matching screenshot style -->
        <div class="page-title-banner">
            list All students records table
        </div>

        <!-- Main Directory Panel -->
        <div class="panel" style="padding: 1.5rem; border-radius: 4px; box-shadow: none; border: 1px solid #bae6fd;">
            
            <!-- Filters Form matching screenshot layout and labels -->
            <form method="GET" action="dashboard.php" class="filter-bar" style="background-color: #e0f2fe; border: 1px solid #bae6fd; padding: 1rem 1.25rem; border-radius: 4px; display: grid; gap: 1rem; align-items: flex-end; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="search" class="form-label" style="font-weight: 700; color: #1e3a8a; font-size: 0.85rem;">Search Record By Name Only</label>
                    <input type="text" id="search" name="search" class="form-input" placeholder="Enter Name..." style="background-color: #ffffff;" value="<?php echo e($search); ?>">
                </div>

                <div class="form-group">
                    <label for="status" class="form-label" style="font-weight: 700; color: #1e3a8a; font-size: 0.85rem;">Select Admission Status</label>
                    <select id="status" name="status" class="form-select" style="background-color: #ffffff;">
                        <option value="">Select Status</option>
                        <option value="Admitted" <?php echo $status === 'Admitted' ? 'selected' : ''; ?>>Admitted</option>
                        <option value="Undecided" <?php echo $status === 'Undecided' ? 'selected' : ''; ?>>Undecided</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="gender" class="form-label" style="font-weight: 700; color: #1e3a8a; font-size: 0.85rem;">Select Gender</label>
                    <select id="gender" name="gender" class="form-select" style="background-color: #ffffff;">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="min_jamb" class="form-label" style="font-weight: 700; color: #1e3a8a; font-size: 0.85rem;">Enter Jamb Score</label>
                    <input type="number" id="min_jamb" name="min_jamb" class="form-input" placeholder="Enter Score..." style="background-color: #ffffff;" min="0" max="400" value="<?php echo e($minJamb); ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-accent" style="width: 100%; font-weight: 700; background-color: #2563eb; color: #ffffff; border: none; padding: 0.65rem 1rem; border-radius: 4px; cursor: pointer; text-transform: lowercase;">
                        search
                    </button>
                </div>
            </form>

            <!-- Students List Table matching screenshot column style -->
            <div class="table-container" style="border: 1px solid #bae6fd; border-radius: 4px; overflow: hidden;">
                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #1e1145; color: #ffffff;">
                            <th style="padding: 0.85rem 1rem; text-align: left; font-weight: 600; font-size: 0.9rem;">S/n</th>
                            <th style="padding: 0.85rem 1rem; text-align: left; font-weight: 600; font-size: 0.9rem;">Name</th>
                            <th style="padding: 0.85rem 1rem; text-align: left; font-weight: 600; font-size: 0.9rem;">Gender</th>
                            <th style="padding: 0.85rem 1rem; text-align: left; font-weight: 600; font-size: 0.9rem;">Jamb Score</th>
                            <th style="padding: 0.85rem 1rem; text-align: left; font-weight: 600; font-size: 0.9rem;">Admission Status</th>
                            <th style="padding: 0.85rem 1rem; text-align: center; font-weight: 600; font-size: 0.9rem;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php 
                            $sn = 1; // Sequential numbering starting from 1
                            foreach ($students as $student): 
                            ?>
                                <tr style="border-bottom: 1px solid #bae6fd;">
                                    <td style="padding: 0.85rem 1rem; color: #1f1c50;"><?php echo $sn++; ?></td>
                                    <td style="padding: 0.85rem 1rem; color: #1f1c50;">
                                        <?php 
                                            $fullName = $student['first_name'];
                                            if (!empty($student['middle_name'])) {
                                                $fullName .= ' ' . $student['middle_name'];
                                            }
                                            $fullName .= ' ' . $student['last_name'];
                                            echo e($fullName);
                                        ?>
                                    </td>
                                    <td style="padding: 0.85rem 1rem; color: #1f1c50;"><?php echo e(strtolower($student['gender'])); ?></td>
                                    <td style="padding: 0.85rem 1rem; color: #1f1c50;"><?php echo intval($student['jamb_score']); ?></td>
                                    <td style="padding: 0.85rem 1rem;">
                                        <?php if ($student['admission_status'] === 'Admitted'): ?>
                                            <span class="badge badge-admitted" style="background-color: #10b981; font-size: 0.8rem; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: 600; text-transform: lowercase; color: #ffffff;"><?php echo e(strtolower($student['admission_status'])); ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-undecided" style="background-color: #4b8cc4; font-size: 0.8rem; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: 600; text-transform: lowercase; color: #ffffff;"><?php echo e(strtolower($student['admission_status'])); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 0.85rem 1rem; text-align: center;">
                                        <a href="view.php?id=<?php echo intval($student['id']); ?>" style="background-color: #2563eb; color: #ffffff; padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" title="View details">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 3rem; text-align: center;">
                                    <p style="color: var(--text-secondary); margin-bottom: 1rem;">No student records match search criteria.</p>
                                    <a href="dashboard.php" class="btn btn-secondary">Clear Filters</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <p>All rights reserved @startocode 2026</p>
        <p>Developer: Isaac Ofori &nbsp;|&nbsp; +233594844398</p>
    </footer>

</body>
</html>
