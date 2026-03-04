<?php
// api/analytics.php
header('Content-Type: application/json');
require 'db.php';

// SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. Get the last 7 days
$dates = [];
for ($i = 6; $i >= 0; $i--) {
    $dates[] = date('Y-m-d', strtotime("-$i days"));
}

// 2. Query only THIS user's data
$sql = "SELECT dl.log_date, COUNT(*) as count 
        FROM daily_logs dl
        JOIN habits h ON dl.habit_id = h.id
        WHERE h.user_id = ? 
        AND dl.status = 'completed' 
        AND dl.log_date >= DATE(NOW() - INTERVAL 7 DAY)
        GROUP BY dl.log_date";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// 3. Format for Chart.js
$result = [];
foreach ($dates as $date) {
    $dayName = date('D', strtotime($date)); 
    $result['labels'][] = $dayName;
    $result['data'][] = isset($data[$date]) ? (int)$data[$date] : 0;
}

echo json_encode($result);
?>