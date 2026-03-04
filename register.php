<?php
require 'api/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // ENCRYPT PASSWORD

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        header("Location: login.php?success=1");
        exit;
    } catch (PDOException $e) {
        $error = "Email already exists!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join HabitMaster</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <div class="card auth-card">
        <h1>🚀 Join HabitMaster</h1>
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required style="width:100%; margin-bottom:10px;">
            <input type="email" name="email" placeholder="Email" required style="width:100%; margin-bottom:10px;">
            <input type="password" name="password" placeholder="Password" required style="width:100%; margin-bottom:10px;">
            <button class="btn-primary" style="width:100%">Create Account</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>