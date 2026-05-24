<?php
header('Content-Type: application/json');
require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed.']);
    exit;
}

// Get the JSON input
$input = json_decode(file_get_contents('php://input'), true);

$id = isset($input['id']) ? intval($input['id']) : 0;
$status = isset($input['status']) ? trim($input['status']) : '';

// Validation
if ($id <= 0 || !in_array($status, ['Admitted', 'Undecided'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input parameters.']);
    exit;
}

try {
    // Verify if student exists
    $checkStmt = $pdo->prepare("SELECT id FROM students WHERE id = :id");
    $checkStmt->execute(['id' => $id]);
    if (!$checkStmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Student record not found.']);
        exit;
    }

    // Update the student admission status
    $updateStmt = $pdo->prepare("UPDATE students SET admission_status = :status WHERE id = :id");
    $updateStmt->execute([
        'status' => $status,
        'id' => $id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Admission status successfully updated to ' . $status . '.'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database update error: ' . $e->getMessage()]);
}
?>
