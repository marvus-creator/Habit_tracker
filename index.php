<?php
require 'api/db.php';
// SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HabitMaster Pro</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <header>
        <div>
            <h1>HabitMaster</h1>
            <div class="user-greeting">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></div>
        </div>
        <a href="logout.php" class="btn-icon" title="Logout">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"></path></svg>
        </a>
    </header>

    <div class="glass-card">
        <canvas id="progressChart"></canvas>
    </div>

    <div class="glass-card" style="padding: 15px;">
        <div class="input-group">
            <input type="text" id="habitName" placeholder="Enter a new task...">
            <input type="text" id="habitIcon" placeholder="🔥" value="⚡">
            <button class="btn-glow" onclick="addHabit()">Add</button>
        </div>
    </div>

    <div id="habitList"></div>
</div>

<script src="js/main.js"></script>
</body>
</html>