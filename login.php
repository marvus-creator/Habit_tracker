<?php
require 'api/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // SUCCESS! Save user to session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HabitMaster</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* --- GLOBAL STYLES --- */
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            color: white;
        }

        /* --- THE GLASS CARD --- */
        .login-box {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-sizing: border-box;
        }

        .login-box h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1.8rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* --- INPUT FIELDS --- */
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .input-group input {
            width: 100%;
            padding: 15px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .input-group input:focus {
            outline: none;
            border-color: #00d2ff;
            background: rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 15px rgba(0, 210, 255, 0.2);
        }

        /* --- THE NEON BUTTON --- */
        .btn-login {
            width: 100%;
            padding: 15px;
            margin-top: 10px;
            background: linear-gradient(45deg, #00d2ff, #3a7bd5);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 210, 255, 0.4);
            font-family: 'Poppins', sans-serif;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 210, 255, 0.6);
        }

        /* --- BOTTOM LINKS --- */
        .links {
            margin-top: 25px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .links a {
            color: #00d2ff;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .links a:hover {
            color: white;
            text-decoration: underline;
        }

        /* --- ALERT MESSAGES --- */
        .alert-success { color: #00b894; margin-bottom: 15px; font-weight: 600; font-size: 0.9rem; }
        .alert-error { color: #ff7675; margin-bottom: 15px; font-weight: 600; font-size: 0.9rem; }
    </style>
</head>
<body>

    <div class="login-box">
        <h2>👋 Welcome Back</h2>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert-success">Account created! Please login.</div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn-login">Login</button>
            
        </form>

        <div class="links">
            New here? <a href="register.php">Create an account</a>
        </div>
    </div>

</body>
</html>