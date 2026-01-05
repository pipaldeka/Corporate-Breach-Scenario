<?php
// scenarios/corporate-breach/webapp/login.php

session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // VULNERABILITY: SQL Injection
    $query = "SELECT * FROM employees WHERE username='$username' AND password=MD5('$password')";
    
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        
        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: admin.php");
            exit();
        } else {
            header("Location: dashboard.php");
            exit();
        }
    } else {
        $error = "Invalid credentials!";
    }
    
    // Debug mode to see query
    if (isset($_GET['debug'])) {
        echo "<div style='background:#f0f0f0; padding:10px; margin:10px; border:1px solid #ccc;'>";
        echo "<strong>Debug Mode - Query:</strong><br>";
        echo "<code>" . htmlspecialchars($query) . "</code>";
        echo "</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Login - CyberCorp</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 400px;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background: #5568d3;
        }
        .error {
            color: red;
            background: #ffebee;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .hint {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        a {
            color: #667eea;
            text-decoration: none;
        }
        code {
            background: #333;
            color: #0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Employee Login</h2>
        
        <?php if ($error): ?>
            <div class="error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        
        <div class="hint">
            <strong>💡 Hints:</strong><br>
            • Try SQL injection techniques<br>
            • Known users: <code>john</code>, <code>admin</code><br>
            • Add <code>?debug=1</code> to URL to see queries<br>
            • Different roles lead to different pages<br>
            • Think: <code>' OR '1'='1'--</code>
        </div>
        
        <p style="text-align:center; margin-top:20px;">
            <a href="index.php">← Back to Home</a>
        </p>
    </div>
</body>
</html>
