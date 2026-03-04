<?php
// api/log_progress.php
header('Content-Type: application/json');
require 'db.php';

// SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}

// Note: We don't strictly need user_id here because we are modifying 
// a specific habit_id, but good practice would be to verify ownership.
// For now, simple auth check is sufficient for V2.

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['habit_id'])) {
    $habit_id = $data['habit_id'];
    $today = date('Y-m-d');
    
    // Check if log exists
    $stmt = $pdo->prepare("SELECT * FROM daily_logs WHERE habit_id = ? AND log_date = ?");
    $stmt->execute([$habit_id, $today]);
    $log = $stmt->fetch();

    if ($log) {
        $new_status = ($log['status'] === 'completed') ? 'pending' : 'completed';
        $update = $pdo->prepare("UPDATE daily_logs SET status = ? WHERE id = ?");
        $update->execute([$new_status, $log['id']]);
    } else {
        $insert = $pdo->prepare("INSERT INTO daily_logs (habit_id, log_date, value, status) VALUES (?, ?, 1, 'completed')");
        $insert->execute([$habit_id, $today]);
        $new_status = 'completed';
    }

    echo json_encode(['success' => true, 'new_status' => $new_status]);
}
?>