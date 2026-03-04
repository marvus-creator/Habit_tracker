<?php
// api/habits.php
header('Content-Type: application/json');
require 'db.php'; // This MUST have session_start() in it!

// SECURITY CHECK: Stop if not logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id']; // Use the logged-in user's ID
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // 1. Fetch habits for THIS user only
    $stmt = $pdo->prepare("SELECT * FROM habits WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    $habits = $stmt->fetchAll();
    
    // 2. Calculate Streaks & Status
    foreach ($habits as &$habit) {
        // A. Check today's status
        $logStmt = $pdo->prepare("SELECT status FROM daily_logs WHERE habit_id = ? AND log_date = CURDATE()");
        $logStmt->execute([$habit['id']]);
        $todayLog = $logStmt->fetch();
        $habit['today_status'] = $todayLog ? $todayLog['status'] : 'pending';

        // B. Calculate Streak
        $streakStmt = $pdo->prepare("SELECT log_date FROM daily_logs WHERE habit_id = ? AND status = 'completed' ORDER BY log_date DESC");
        $streakStmt->execute([$habit['id']]);
        $dates = $streakStmt->fetchAll(PDO::FETCH_COLUMN);

        $current_streak = 0;
        $check_date = new DateTime(); 
        $today_str = $check_date->format('Y-m-d');

        if ($habit['today_status'] !== 'completed') {
            $check_date->modify('-1 day');
        }

        foreach ($dates as $date_str) {
            if ($date_str == $check_date->format('Y-m-d')) {
                $current_streak++;
                $check_date->modify('-1 day'); 
            } elseif ($date_str == $today_str) {
                continue;
            } else {
                break;
            }
        }
        $habit['streak'] = $current_streak;
    }
    echo json_encode($habits);

} elseif ($method === 'POST') {
    // 3. Add new habit
    $data = json_decode(file_get_contents("php://input"), true);
    if (!empty($data['habit_name'])) {
        $stmt = $pdo->prepare("INSERT INTO habits (user_id, habit_name, habit_icon, habit_color) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $user_id, 
            $data['habit_name'], 
            $data['habit_icon'] ?? '✅', 
            '#4CAF50'
        ]);
        echo json_encode(['success' => true]);
    }

} elseif ($method === 'DELETE') {
    // 4. Delete habit
    $data = json_decode(file_get_contents("php://input"), true);
    if (!empty($data['id'])) {
        // Only delete if it belongs to the current user!
        $stmt = $pdo->prepare("DELETE FROM habits WHERE id = ? AND user_id = ?");
        $stmt->execute([$data['id'], $user_id]);
        echo json_encode(['success' => true]);
    }
}
?>